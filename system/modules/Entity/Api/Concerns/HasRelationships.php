<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Api\Concerns;

use Module\Entity\Api\Builder;
use Module\Entity\Api\Entity;
use Module\Entity\Api\Relations\BelongsTo;
use Module\Entity\Api\Relations\BelongsToMany;
use Module\Entity\Api\Relations\HasMany;
use Module\Entity\Api\Relations\HasOne;

trait HasRelationships
{
    /** @var Builder */
    protected $query;

    /**
     * Define a one-to-one relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @param string $value
     * @return HasOne
     */
    public function hasOne($related, $foreignKey, $localKey, $value)
    {
        $instance = $this->newRelatedQuery($related);

        return new HasOne(
            $instance, $this->query->getModel(), $instance->getModel()->getTable().'.'.$foreignKey, $localKey, $value
        );
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $ownerKey
     * @param string $value
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey, $ownerKey, $value)
    {
        return new BelongsTo(
            $this->newRelatedQuery($related), $this->query->getModel(), $foreignKey, $ownerKey, $value
        );
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @param string $value
     * @return HasMany
     */
    public function hasMany($related, $foreignKey, $localKey, $value)
    {
        $instance = $this->newRelatedQuery($related);

        return new HasMany(
            $instance, $this->query->getModel(), $instance->getModel()->getTable().'.'.$foreignKey, $localKey, $value
        );
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param string $related
     * @param Entity $local
     * @return BelongsToMany
     */
    public function belongsToMany($related, Entity $local) {
        $instance = $this->newRelatedQuery($related);

        $foreignPivotKey = $local->getForeignKey();
        $relatedPivotKey = $instance->getModel()->getForeignKey();
        $table = $this->joiningTable($instance->getModel()->getTable(), $local->getTable());

        return new BelongsToMany(
            $instance, $this->query->getModel(), $table, $foreignPivotKey,
            $relatedPivotKey, 'id', 'id', $local->id
        );
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param string $related
     * @param string $local
     * @return string
     */
    public function joiningTable($related, $local)
    {
        $models = [$related, $local];

        sort($models);

        return strtolower(implode('_', $models));
    }

    /**
     * Create a new model instance for a related model.
     *
     * @param string $class
     * @return mixed
     */
    protected function newRelatedQuery($class)
    {
        return $this->query->newQueryFor(new $class);
    }
}
