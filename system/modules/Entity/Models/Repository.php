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
use Sydes\Http\Request;
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

    public function filteredAndSorted(Request $req)
    {
        $query = $this->newQuery();

        foreach ($req->input('filter', []) as $field => $criterion) {
            if ($this->model->hasField($field) && ($filter = $this->parseCriterion($criterion)) !== null) {
                $query = $this->applyFilter($field, $filter, $query);
            }
        }

        if ($req->has('by')) {
            $query = $query->orderBy($req->input('by'), $req->input('order', 'desc'));
        } else {
            $query = $query->orderByDesc('id');
        }

        return $query;
    }

    protected function parseCriterion($value){
        $ret = null;

        if (!preg_match("/([!<>]?[*>=]?) ?([{}\w ,'-]+) ?(\*?)/iu", $value, $out)) {
            return $ret;
        }

        if (empty($out[1])) {
            if (strpos($out[2], ',') !== false) {
                $ret = ['in'];
                foreach (explode(',', $out[2]) as $item) {
                    $ret[1][] = trim($item);
                }
            } elseif (strpos($out[2], '-') !== false) {
                $val = explode('-', $out[2], 2);
                $val[0] = trim($val[0]);
                $val[1] = trim($val[1]);
                if (is_numeric($val[0]) && is_numeric($val[1])) {
                    $ret = ['between', $val];
                }
            } elseif ($out[3] == '*') {
                $ret = ['begins_with', $out[2]];
            } else {
                $ret = ['equal', $out[2]];
            }
        } elseif ($out[1] == '=') {
            $ret = ['equal', $out[2]];
            if ($out[2] == "''") {
                $ret[0] = 'is_empty';
            }
        } elseif ($out[1] == '!=' || $out[1] == '<>') {
            $ret = ['not_equal', $out[2]];
            if ($out[2] == "''") {
                $ret[0] = 'is_not_empty';
            }
        } elseif ($out[1] == '!') {
            if (strpos($out[2], ',') !== false) {
                $ret = ['not_in'];
                foreach (explode(',', $out[2]) as $item) {
                    $ret[1][] = trim($item);
                }
            } elseif (strpos($out[2], '-') !== false) {
                $val = explode('-', $out[2], 2);
                $val[0] = trim($val[0]);
                $val[1] = trim($val[1]);
                if (is_numeric($val[0]) && is_numeric($val[1])) {
                    $ret = ['not_between', $val];
                }
            } elseif ($out[3] == '*') {
                $ret = ['not_begins_with', $out[2]];
            } else {
                $ret = ['not_equal', $out[2]];
            }
        } elseif ($out[1] == '<') {
            $ret = ['less', $out[2]];
        } elseif ($out[1] == '<=') {
            $ret = ['less_or_equal', $out[2]];
        } elseif ($out[1] == '>') {
            $ret = ['greater', $out[2]];
        } elseif ($out[1] == '>=') {
            $ret = ['greater_or_equal', $out[2]];
        } elseif ($out[1] == '*') {
            if ($out[3] == '*') {
                $ret = ['contains', $out[2]];
            } else {
                $ret = ['ends_with', $out[2]];
            }
        } elseif ($out[1] == '!*') {
            if ($out[3] == '*') {
                $ret = ['not_contains', $out[2]];
            } else {
                $ret = ['not_ends_with', $out[2]];
            }
        }

        return $ret;
    }

    protected function applyFilter($field, array $filter, Builder $query, $boolean = 'and') {
        $value = $filter[1];

        switch ($filter[0]) {
            case 'equal':
                $query->where($field, '=', $value, $boolean);
                break;
            case 'not_equal':
                $query->where($field, '<>', $value, $boolean);
                break;
            case 'in':
                $query->whereIn($field, (array)$value, $boolean);
                break;
            case 'not_in':
                $query->whereNotIn($field, (array)$value, $boolean);
                break;
            case 'less':
                $query->where($field, '<', $value, $boolean);
                break;
            case 'less_or_equal':
                $query->where($field, '<=', $value, $boolean);
                break;
            case 'greater':
                $query->where($field, '>', $value, $boolean);
                break;
            case 'greater_or_equal':
                $query->where($field, '>=', $value, $boolean);
                break;
            case 'between':
                $query->whereBetween($field, (array)$value, $boolean);
                break;
            case 'not_between':
                $query->whereNotBetween($field, (array)$value, $boolean);
                break;
            case 'begins_with':
                $query->where($field, 'like', "{$value}%", $boolean);
                break;
            case 'not_begins_with':
                $query->where($field, 'not like', "{$value}%", $boolean);
                break;
            case 'contains':
                $query->where($field, 'like', "%{$value}%", $boolean);
                break;
            case 'not_contains':
                $query->where($field, 'not like', "%{$value}%", $boolean);
                break;
            case 'ends_with':
                $query->where($field, 'like', "%{$value}", $boolean);
                break;
            case 'not_ends_with':
                $query->where($field, 'not like', "%{$value}", $boolean);
                break;
            case 'is_empty':
                $query->where($field, '=', '', $boolean);
                break;
            case 'is_not_empty':
                $query->where($field, '<>', '', $boolean);
                break;
            case 'is_null':
                $query->whereNull($field, $boolean);
                break;
            case 'is_not_null':
                $query->whereNotNull($field, $boolean);
                break;
        }

        return $query;
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
