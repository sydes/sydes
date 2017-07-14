<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Database\Connection;
use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Database\Schema\Blueprint;
use Sydes\Support\Collection;

class Repository
{
    /** @var string class name */
    protected $entity;
    /** @var Entity instance */
    protected $model;
    /** @var Connection */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;

        if ($this->entity) {
            $this->forEntity($this->entity);
        }
    }

    /**
     * Set class name of entity
     *
     * @param string $class class name
     * @return $this
     */
    public function forEntity($class)
    {
        $this->entity = $class;
        $this->model = new $class;

        return $this;
    }

    /**
     * Set instance of entity
     *
     * @param Entity $model
     * @return $this
     */
    public function setModel(Entity $model)
    {
        $this->entity = get_class($model);
        $this->model = $model;

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
     * Get all of the models from the database.
     *
     * @param  array $columns
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->newQuery()->get($columns);
    }

    /**
     * Save the model to the database
     *
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity)
    {
        $this->setModel($entity);

        if ($this->fireModelEvent('saving', true) === false) {
            return false;
        }

        $query = $this->newQuery();
        $saved = $entity->exists ? $this->update($query, $entity) : $this->insert($query, $entity);

        if ($saved) {
            $this->fireModelEvent('saved');
            $entity->clean();
        }

        return $saved;
    }

    /**
     * Perform a model update operation.
     *
     * @param Builder $query
     * @param Entity  $entity
     * @return bool
     */
    protected function update(Builder $query, Entity $entity)
    {
        if ($entity->isClean() || $this->fireModelEvent('updating', true) === false) {
            return false;
        }

        $query->where('id', '=', $entity->id)->update($entity->toStorage());

        $this->fireModelEvent('updated');

        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @param Builder $query
     * @param Entity  $entity
     * @return bool
     */
    protected function insert(Builder $query, Entity $entity)
    {
        if ($this->fireModelEvent('creating', true) === false) {
            return false;
        }

        $entity->id = $query->insertGetId($entity->toStorage(), 'id');
        $entity->exists = true;

        $this->fireModelEvent('created');

        return true;
    }

    /**
     * Delete the model from the database
     *
     * @param Entity $entity
     * @return int
     */
    public function delete(Entity $entity)
    {
        $this->setModel($entity);

        if ($this->fireModelEvent('deleting', true) === false) {
            return false;
        }

        if ($result = $this->newQuery()->delete($entity->id)) {
            $entity->exists = false;
        }

        $this->fireModelEvent('deleted');

        return $result;
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param  array|int $ids
     * @return int
     */
    public function destroy($ids)
    {
        $ids = is_array($ids) ? $ids : func_get_args();

        return $this->newQuery()->whereIn('id', $ids)->delete();
    }

    /**
     * Create table for this entity
     */
    public function makeTable()
    {
        $table = $this->model->getTable();
        $schema = $this->db->getSchemaBuilder();

        $schema->create($table, function (Blueprint $t) {
            $this->model->makeTable($t, $this->db);
        });

        if ($this->model->hasLocalized()) {
            $schema->create($table.'_localized', function (Blueprint $t) use ($table) {
                $t->integer('entity_id')->unsigned();
                $t->string('locale');

                foreach ($this->model->getLocalized() as $name) {
                    $this->model->field($name)->onCreate($t, $this->db);
                }

                $t->foreign('entity_id')->references('id')->on($table)->onDelete('cascade');
                $t->primary(['entity_id', 'locale']);
            });
        }

        if ($this->model->usesEav()) {
            $schema->create($table.'_eav', function (Blueprint $t) use ($table) {
                $t->increments('id');
                $t->integer('entity_id')->unsigned();
                $t->string('key');
                $t->string('value')->nullable();

                $t->foreign('entity_id')->references('id')->on($table)->onDelete('cascade');
                $t->unique(['entity_id', 'key']);
            });
        }
    }

    /**
     * Drop table of this entity
     */
    public function dropTable()
    {
        $table = $this->model->getTable();
        $schema = $this->db->getSchemaBuilder();

        $this->model->dropTable($this->db);

        if ($this->model->hasLocalized()) {
            $schema->drop($table.'_localized');
        }
        if ($this->model->usesEav()) {
            $schema->drop($table.'_eav');
        }
        $schema->drop($table);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder
     */
    public function newQuery()
    {
        if (!$this->model) {
            throw new \RuntimeException("Entity not set in this repository");
        }

        $builder = new Builder($this->newQueryBuilder());

        return $builder->setModel($this->model);
    }

    /**
     * @return QueryBuilder
     */
    protected function newQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }

    /**
     * @param string $key
     * @param bool   $halt
     * @return bool
     */
    protected function fireModelEvent($key, $halt = false)
    {
        return $this->model->fire($key, $this->db, $halt);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->newQuery(), $method], $args);
    }
}
