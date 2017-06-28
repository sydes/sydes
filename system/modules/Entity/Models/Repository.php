<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Db;

class Repository
{
    /** @var \PDO */
    protected $db;
    /** @var Entity */
    protected $model;
    protected $entity;
    protected $query;

    public function __construct(Db $query)
    {
        $this->query = $query;
        $this->db = $query->pdo();

        if ($this->entity) {
            $this->model($this->entity);
        }
    }

    /**
     * @param string $model class name
     * @return $this
     */
    public function model($model)
    {
        $this->entity = $model;
        $this->model = new $model;

        $this->query = $this->query
            ->table($this->model->getTable())
            ->setFetchMode(\PDO::FETCH_ASSOC);

        return $this;
    }

    /**
     * @return Entity
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Creates table from entity fields
     */
    public function makeTable()
    {
        $table = $this->model->getTable();
        $fields = $this->model->getFields();

        if ($this->model->hasLocalized()) {
            $cols = [
                "entity_id INTEGER NOT NULL REFERENCES {$table}(id) ON DELETE CASCADE",
                'locale TEXT NOT NULL',
            ];

            foreach ($this->model->getLocalized() as $col) {
                $cols = $fields[$col]->onCreate($cols);
                unset($fields[$col]);
            }

            $cols[] = 'UNIQUE (entity_id, locale)';

            $this->db->exec("CREATE TABLE {$table}_localized (\n".implode(",\n", $cols)."\n)");
        }

        $cols = [
            $this->model->getKeyName().' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT',
        ];

        foreach ($fields as $field) {
            $cols = $field->onCreate($cols);
        }

        if ($this->model->usesTimestamps()) {
            $cols[] = 'created_at INTEGER';
            $cols[] = 'updated_at INTEGER';
        }

        $this->db->exec("CREATE TABLE {$table} (\n".implode(",\n", $cols)."\n)");

        if ($this->model->usesEav()) {
            $this->db->exec("CREATE TABLE {$table}_eav (
id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
entity_id INTEGER NOT NULL REFERENCES {$table}(id) ON DELETE CASCADE,
key TEXT NOT NULL,
value TEXT,
UNIQUE (entity_id, key)\n)");
        }
    }

    /**
     * Removes table for this entity
     */
    public function dropTable()
    {
        $table = $this->model->getTable();

        if ($this->model->hasLocalized()) {
            $this->db->drop($table.'_localized');
        }

        if ($this->model->usesEav()) {
            $this->db->drop($table.'_eav');
        }

        $this->db->drop($table);
    }

    public function first()
    {
        return new $this->entity($this->query->first());
    }

    public function all()
    {
        $models = [];
        foreach ($this->query->get() as $row) {
            $models[] = new $this->entity($row);
        }

        return $models;
    }

    public function save(Entity $entity)
    {

    }

    public function destroy(Entity $entity)
    {

    }

    public function __call($name, $args) {
        if (substr($name, 0, 6) == 'findBy') {
            $this->query->find($args[0], strtolower(substr($name, 6)));
        }

        call_user_func_array([$this->query, $name], $args);

        return $this;
    }
}
