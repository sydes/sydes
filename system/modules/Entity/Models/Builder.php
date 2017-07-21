<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Database\Concerns\BuildsQueries;
use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Support\Collection;

class Builder
{
    use BuildsQueries;

    /** @var QueryBuilder */
    protected $query;
    /** @var Entity */
    protected $model;

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = [
        'insert', 'insertGetId', 'getBindings', 'toSql', 'exists',
        'count', 'min', 'max', 'avg', 'sum', 'getConnection',
    ];

    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Find a model by its primary key.
     *
     * @param int|array $id
     * @param array     $columns
     * @return Entity|Collection|null
     */
    public function find($id, $columns = ['*'])
    {
        if (is_array($id)) {
            return empty($id) ?
                new Collection() :
                $this->whereIn($this->model->getQualifiedKeyName(), $id)->get($columns);
        }

        return $this->where($this->model->getQualifiedKeyName(), $id)->first($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param int|array $id
     * @param array     $columns
     * @return Entity|Collection
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        if (is_array($id)) {
            if (count($result) == count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        throw new \RuntimeException('Entity '.get_class($this->model).' with id='.$id.' not found');
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     * @return mixed
     */
    public function value($column)
    {
        if ($result = $this->first([$column])) {
            return $result->{$column};
        }
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        $models = $this->getModels($columns);

        return $models;
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param array $columns
     * @return Collection
     */
    public function getModels($columns = ['*'])
    {
        return $this->hydrate(
            $this->query->get($columns)->all()
        );
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @return Collection
     */
    public function hydrate(array $items)
    {
        $instance = $this->model;
        return new Collection(array_map(function ($item) use ($instance) {
            return $instance->newFromStorage($item);
        }, $items));
    }

    /**
     * Add a generic "order by" clause if the query doesn't already have one.
     *
     * @return void
     */
    protected function enforceOrderBy()
    {
        if (empty($this->query->orders) && empty($this->query->unionOrders)) {
            $this->orderBy($this->model->getQualifiedKeyName(), 'asc');
        }
    }

    /**
     * Get the model instance being queried.
     *
     * @return Entity
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param  Entity $model
     * @return $this
     */
    public function setModel(Entity $model)
    {
        $this->model = $model;
        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param  QueryBuilder $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->passthru)) {
            return call_user_func_array([$this->query, $method], $args);
        }

        call_user_func_array([$this->query, $method], $args);

        return $this;
    }
}
