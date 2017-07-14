<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

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
     * Gets value as is or his part
     *
     * @param string $key
     * @return mixed
     */
    public function value($key = null);

    /**
     * Gets name of field
     *
     * @return mixed
     */
    public function name();

    /**
     * Gets all settings for field or one if key is provided
     *
     * @param string|null $key
     * @return array|mixed
     */
    public function getSettings($key = null);

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function setSettings($key, $value = null);

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
     * @param Blueprint $t main table
     * @param Connection $db
     * @return array
     */
    public function onCreate(Blueprint $t, Connection $db);

    /**
     * Triggered on entity table deletion
     *
     * @param Connection $db
     */
    public function onDrop(Connection $db);

    /**
     * Triggered before entity is saved
     *
     * Returning a false will halt the save operation
     *
     * @param Connection $db
     * @return bool
     */
    public function saving(Connection $db);

    /**
     * Triggered after entity was saved.
     *
     * @param Connection $db
     */
    public function saved(Connection $db);

    /**
     * Triggered before entity is created
     *
     * Returning a false will halt the save operation
     *
     * @param Connection $db
     * @return bool
     */
    public function creating(Connection $db);

    /**
     * Triggered after entity was created.
     *
     * @param Connection $db
     */
    public function created(Connection $db);

    /**
     * Triggered before entity is updated
     *
     * Returning a false will halt the save operation
     *
     * @param Connection $db
     * @return bool
     */
    public function updating(Connection $db);

    /**
     * Triggered after entity was updated.
     *
     * @param Connection $db
     */
    public function updated(Connection $db);

    /**
     * Triggered before entity is deleted
     *
     * Returning a false will halt the delete operation
     *
     * @param Connection $db
     * @return bool
     */
    public function deleting(Connection $db);

    /**
     * Triggered after entity was deleted.
     *
     * @param Connection $db
     */
    public function deleted(Connection $db);
}
