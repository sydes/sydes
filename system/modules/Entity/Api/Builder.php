<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Api;

use Module\Entity\Api\Relations\Relation;
use Sydes\Database\Concerns\BuildsQueries;
use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Support\Collection;
use Sydes\Support\Str;

class Builder
{
    use BuildsQueries;

    /** @var QueryBuilder */
    protected $query;
    /** @var Entity */
    protected $model;

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $eagerLoad = [];

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

        return null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        if (count($models = $this->getModels($columns)) > 0) {
            $models = $this->eagerLoadRelations($models);
        }

        return collect($models);
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param array $columns
     * @return array
     */
    public function getModels($columns = ['*'])
    {
        return $this->hydrate(
            $this->query->get($columns)->all()
        );
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param array $models
     * @return array
     */
    public function eagerLoadRelations(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            // For nested eager loads we'll skip loading them here and they will be set as an
            // eager load on the query to retrieve the relation so that they will be eager
            // loaded on that query, because that is where they get hydrated as models.
            if (strpos($name, '.') === false) {
                $models = $this->eagerLoadRelation($models, $name, $constraints);
            }
        }

        return $models;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param array    $models
     * @param string   $name
     * @param \Closure $constraints
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, \Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);
        $relation->addEagerConstraints($models);
        $constraints($relation);
        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.

        // TODO not default and not per model but field
        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->getEager(), $name
        );
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param string $name
     * @return Relation
     */
    public function getRelation($name)
    {
        // We want to run a relationship query without any constrains so that we will
        // not have to remove these where clauses manually which gets really hacky
        // and error prone. We don't want constraints because we add eager ones.
        $relation = Relation::noConstraints(function () use ($name) {
            try {
                return $this->getModel()->field($name)->relation();
                // TODO check errors
            } catch (\BadMethodCallException $e) {
                throw new \RuntimeException('Call to undefined relationship '.$name);
            }
        });

        $nested = $this->relationsNestedUnder($name);
        // If there are nested relationships set on the query, we will put those onto
        // the query instances so that they can be handled after this relationship
        // is loaded. In this way they will all trickle down as they are loaded.
        if (count($nested) > 0) {
            $relation->getQuery()->with($nested);
        }

        return $relation;
    }

    /**
     * Get the deeply nested relations for a given top-level relation.
     *
     * @param string $relation
     * @return array
     */
    protected function relationsNestedUnder($relation)
    {
        $nested = [];
        // We are basically looking for any relationships that are nested deeper than
        // the given top-level relationship. We will just check for any relations
        // that start with the given top relations and adds them to our arrays.
        foreach ($this->eagerLoad as $name => $constraints) {
            if ($this->isNestedUnder($relation, $name)) {
                $nested[substr($name, strlen($relation.'.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Determine if the relationship is nested.
     *
     * @param string $relation
     * @param string $name
     * @return bool
     */
    protected function isNestedUnder($relation, $name)
    {
        return Str::contains($name, '.') && Str::startsWith($name, $relation.'.');
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param array $items
     * @return array
     */
    public function hydrate(array $items)
    {
        $instance = $this->model;
        return array_map(function ($item) use ($instance) {
            return $instance->newFromStorage($item, $this->newQuery());
        }, $items);
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
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relations
     * @return $this
     */
    public function with($relations)
    {
        $eagerLoad = $this->parseWithRelations(is_string($relations) ? func_get_args() : $relations);

        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);

        return $this;
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param array $relations
     * @return array
     */
    protected function parseWithRelations(array $relations)
    {
        $results = [];
        foreach ($relations as $name => $constraints) {
            // If the "relation" value is actually a numeric key, we can assume that no
            // constraints have been specified for the eager load and we'll just put
            // an empty Closure with the loader so that we can treat all the same.
            if (is_numeric($name)) {
                $name = $constraints;
                list($name, $constraints) = Str::contains($name, ':')
                    ? $this->createSelectWithConstraint($name)
                    : [$name, function () {
                        //
                    }];
            }
            // We need to separate out any nested includes. Which allows the developers
            // to load deep relationships using "dots" without stating each level of
            // the relationship with its own key in the array of eager load names.
            $results = $this->addNestedWiths($name, $results);
            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Create a constraint to select the given columns for the relation.
     *
     * @param string $name
     * @return array
     */
    protected function createSelectWithConstraint($name)
    {
        return [explode(':', $name)[0], function ($query) use ($name) {
                $query->select(explode(',', explode(':', $name)[1]));
            }];
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param string $name
     * @param array  $results
     * @return array
     */
    protected function addNestedWiths($name, $results)
    {
        $progress = [];
        // If the relation has already been set on the result array, we will not set it
        // again, since that would override any constraints that were already placed
        // on the relationships. We will only set the ones that are not specified.
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;
            if (!isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function () {
                    //
                };
            }
        }

        return $results;
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
     * @param Entity $model
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
     * @param QueryBuilder $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder
     */
    public function newQuery()
    {
        return (new static($this->query->newQuery()))->setModel($this->model);
    }

    /**
     * @param Entity $model
     * @return mixed
     */
    public function newQueryFor(Entity $model)
    {
        return (new static($this->query->newQuery()))->setModel($model);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param string $method
     * @param array  $args
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

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
