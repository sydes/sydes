<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

interface EntityInterface
{
    /**
     * @param array $attrs
     * @return $this
     */
    public function fill(array $attrs);

    /**
     * Init field and return him
     *
     * @param string $name
     * @return FieldInterface
     */
    public function field($name);

    /**
     * Init all fields and return them
     *
     * @return FieldInterface[]
     */
    public function allFields();

    /**
     * Returns raw data
     *
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @param mixed $table
     */
    public function setTable($table);

    /**
     * Returns field list config
     *
     * @return array
     */
    public function getFields();

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields($fields);

    /**
     * @return bool
     */
    public function usesTimestamps();

    /**
     * @param bool $val
     * @return $this
     */
    public function useTimestamps($val = true);

    /**
     * @return string
     */
    public function getKeyName();

    /**
     * Get the value of the model's primary key.
     *
     * @return int
     */
    public function getKey();

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey();

    /**
     * @return bool
     */
    public function usesEav();

    /**
     * @param bool $val
     * @return $this
     */
    public function useEav($val = true);

    /**
     * @return bool
     */
    public function hasLocalized();

    /**
     * @return array
     */
    public function getLocalized();

    /**
     * @param array $fields
     * @return $this
     */
    public function setLocalized(array $fields = []);

    /**
     * Triggered on entity table creation
     *
     * @return string
     */
    public function makeTable();

    /**
     * Triggered on entity table deletion
     */
    public function dropTable();

    /**
     * Triggered before entity is saved
     *
     * Returning a false will halt the save operation
     *
     * @return bool
     */
    public function beforeSave();

    /**
     * Triggered after entity was saved.
     */
    public function afterSave();

    /**
     * Triggered before entity is deleted
     *
     * Returning a false will halt the delete operation
     *
     * @return bool
     */
    public function beforeDelete();

    /**
     * Triggered after entity was deleted.
     */
    public function afterDelete();
}
