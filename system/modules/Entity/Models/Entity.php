<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Database\Connection;
use Sydes\Database\Query\Builder;
use Sydes\Database\Schema\Blueprint;

class Entity
{
    // Settings
    protected $table;
    protected $fields = [];
    protected $eav = false;
    protected $localized = [];
    protected $perPage = 15;

    // Public data
    public $id;
    public $exists = false;

    // Private data
    protected $changed = [];
    protected $bootedFields = [];
    protected $booted = false;

    /**
     * Entity constructor
     *
     * @param array $attrs
     */
    public function __construct(array $attrs = [])
    {
        $this->fill($attrs);
    }

    /**
     * Fill the model with an array of attributes
     *
     * @param array $attrs
     * @return $this
     */
    public function fill(array $attrs)
    {
        foreach (array_intersect_key($attrs, $this->fields) as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->changed[$key] = 1;
        $this->field($key)->set($value);

        return $this;
    }

    /**
     * Get raw data from fields
     */
    public function toArray()
    {
        return collect($this->getFields())->map(function (Field $item) {
            return $item->value();
        })->all();
    }

    /**
     * Convert the model's data to an array for inserting to database
     *
     * @return string
     */
    public function toStorage()
    {
        return collect($this->getFields())->map(function (Field $item) {
            return $item->toString();
        })->all();
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        }
        $name = get_class($this);

        return snake_case(($pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : $name);
    }

    /**
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->getTable().'_id';
    }

    /**
     * @return bool
     */
    public function usesEav()
    {
        return $this->eav;
    }

    /**
     * @return $this
     */
    public function useEav()
    {
        $this->eav = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLocalized()
    {
        return !empty($this->localized);
    }

    /**
     * @return array
     */
    public function getLocalized()
    {
        return $this->localized;
    }

    /**
     * @param array $fields
     * @return Entity
     */
    public function setLocalized(array $fields = [])
    {
        $this->localized = $fields;

        return $this;
    }

    /**
     * @param array|\stdClass $attrs
     * @return Entity
     */
    public function newFromStorage($attrs = [])
    {
        $attrs = (array)$attrs;

        $model = clone $this;
        $model->exists = true;
        $model->id = array_remove($attrs, 'id');

        foreach ($attrs as $key => $value) {
            $model->field($key)->fromString($value);
        }

        return $model;
    }

    /**
     * Fire event in fields on entity table creation
     *
     * @param Blueprint  $t
     * @param Connection $db
     */
    public function makeTable(Blueprint $t, Connection $db)
    {
        $t->increments('id');
        foreach ($this->getFields() as $name => $field) {
            if (!isset($this->localized[$name])) {
                $field->onCreate($t, $db);
            }
        }
    }

    /**
     * Fire event in fields on entity table deletion
     *
     * @param Connection $db
     */
    public function dropTable(Connection $db)
    {
        foreach ($this->getFields() as $field) {
            $field->onDrop($db);
        }
    }

    /**
     * Fire some event in fields
     *
     * @param string  $event
     * @param Builder $query
     * @param bool    $halt
     * @return bool
     */
    public function fire($event, Connection $db, $halt = false)
    {
        if ($halt) {
            foreach ($this->getFields() as $field) {
                if ($field->$event($db) === false) {
                    return false;
                }
            }
        } else {
            foreach ($this->getFields() as $field) {
                $field->$event($db);
            }
        }

        return true;
    }

    /**
     * Boot all fields and return them
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        if (!$this->booted) {
            $this->booted = true;
            foreach ($this->fields as $name => $void) {
                $this->field($name);
            }
        }

        return $this->bootedFields;
    }

    /**
     * Init field and return him
     *
     * @param string $name
     * @return FieldInterface
     */
    public function field($name)
    {
        if (!isset($this->bootedFields[$name])) {
            $this->bootedFields[$name] = $this->bootField($name);
        }

        return $this->bootedFields[$name];
    }

    /**
     * @param $name
     * @return FieldInterface
     */
    protected function bootField($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException('Field '.$name.' not found in '.get_class($this));
        }

        $field = $this->fields[$name];

        if (!isset($field['settings'])) {
            $field['settings'] = [];
        }

        $class = app('form.fields')[$field['type']];

        return new $class($name, null, $field['settings']);
    }

    /**
     * Returns field list config
     *
     * @return array
     */
    public function getFieldList()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $key
     * @param array  $field with type, settings and position
     * @return $this
     */
    public function addField($key, array $field)
    {
        $this->fields[$key] = $field;

        return $this;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null $attrs
     * @return bool
     */
    public function isDirty($attrs = null)
    {
        if (is_null($attrs)) {
            return count($this->changed) > 0;
        }

        foreach ((array)$attrs as $attr) {
            if (isset($this->changed[$attr])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the model or given attribute(s) have remained the same.
     *
     * @param  array|string|null $attrs
     * @return bool
     */
    public function isClean($attrs = null)
    {
        return !$this->isDirty($attrs);
    }

    /**
     * Mark model as saved
     */
    public function clean()
    {
        $this->changed = [];
    }

    /**
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        return $this->field($key)->render();
    }

    /**
     * @param string $key
     * @param string $value
     * @return Entity
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->fields);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->set($key, '');
    }
}
