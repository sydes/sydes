<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

interface FieldInterface
{
    /**
     * Field constructor with data and settings
     *
     * @param string $name
     * @param string $value
     * @param array  $settings
     */
    public function __construct($name, $value, $settings = []);

    /**
     * Sets value from database. Can unserialize to array
     *
     * @param mixed $value
     * @return $this
     */
    public function fromString($value);

    /**
     * Gets string formatted value for database
     *
     * @return string
     */
    public function toString();

    /**
     * Sets value as is
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value);

    /**
     * Gets value as is
     *
     * @return mixed
     */
    public function get();

    /**
     * Gets name of field
     *
     * @return mixed
     */
    public function getName();

    /**
     * Gets all settings for field or one if key is provided
     *
     * @param string|null $key
     * @return array|mixed
     */
    public function getSettings($key = null);

    /**
     * Gets list of available value formatters for renderer
     *
     * @return array
     */
    public function getFormatters();

    /**
     * Return false to cancel saving
     *
     * @return bool
     */
    public function validate();

    /**
     * Gets form input with wrapper
     *
     * @param callable $wrapper
     * @return string
     */
    public function formInput($wrapper = null);

    /**
     * Gets only input
     *
     * @return string
     */
    public function input();

    /**
     * Gets form with settings for this field
     *
     * @return string
     */
    public function formSettings();

    /**
     * Defines how the field will actually display its contents on front
     *
     * If $formatter provided, it will be used for render else default or one from settings
     *
     * @param callable $formatter
     * @return string
     */
    public function render($formatter = null);

    /**
     * Triggered on entity table creation
     *
     * @param array $cols
     * @return array
     */
    public function onCreate(array $cols);

    /**
     * Triggered on entity table deletion
     */
    public function onDrop();

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
