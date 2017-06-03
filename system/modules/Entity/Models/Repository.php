<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Models;

use Sydes\Database;
use Sydes\PDO;

class Repository
{
    /** @var PDO */
    protected $db;
    /** @var Entity */
    protected $model;
    /** @var PDO */
    protected $result;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $model Model name
     * @return $this
     */
    public function forModel($model)
    {
        $this->model = model($model);
        $this->db = $this->db->connection($this->model->getConnection());

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
                $cols[] = $fields[$col]->getSchema();
                unset($fields[$col]);
            }

            $cols[] = 'UNIQUE (entity_id, locale)';

            $this->db->exec("CREATE TABLE {$table}_localized (\n".implode(",\n", $cols)."\n)");
        }

        $cols = [
            $this->model->getPk().' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT',
        ];

        foreach ($fields as $field) {
            $cols[] = $field->getSchema();
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

    /**
     * @param mixed $criteria
     * @param array $cols
     * @param array $opts
     * @return $this
     */
    public function find($criteria, array $cols = ['*'], $opts = [])
    {
        if (is_numeric($criteria)) {
            $criteria = ['id', $criteria];
        }

        // если $criterion[0] не поле и не мета, искать id в локализованных или еав
        // найдя id
        // или иначе, получить данные со всех таблиц
        $this->result = $this->db->select($this->model->getTable(), $cols, $criteria, $opts);

        return $this;
    }

    public function first()
    {
        $model = clone $this->model;
        $item = $this->result->first();

        return $model->fill($item)->withProps($item);
    }

    public function all()
    {
        $results = [];
        foreach ($this->result->all() as $item) {
            $model = clone $this->model;
            $results[] = $model->fill($item)->withProps($item);
        }

        return $results;
    }

    public function save(Entity $entity)
    {

    }

    public function destroy(Entity $entity)
    {

    }

    public function __call($name, $args) {
        if (substr($name, 0, 6) == 'findBy') {
            $field = strtolower(substr($name, 6));

            if (!isset($args[1])) {
                $args[1] = ['*'];
            }

            return $this->find([$field, $args[0]], $args[1]);
        }

        throw new \InvalidArgumentException('Wrong method '.$name);
    }
}
// TODO написать класс, который на основе этой модели будет работать с базой.
// на основе id обновлять или удалять данные в таблице, полученной через getTable
// в репозитории уже его использовать
