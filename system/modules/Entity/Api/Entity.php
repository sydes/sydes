<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Api;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class Entity implements EntityInterface
{
    use Concerns\HasFields;

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
    protected $builder;

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
     * @return array
     */
    public function toStorage()
    {
        $data = [];
        foreach ($this->getFields() as $name => $field) {
            if (($value = $field->toString()) !== null) {
                $data[$name] = $value;
            }
        }

        return $data;
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
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->getTable().'.id';
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
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @param array|\stdClass $attrs
     * @param Builder $builder
     * @return Entity
     */
    public function newFromStorage($attrs = [], Builder $builder = null)
    {
        $attrs = (array)$attrs;

        $model = clone $this;
        $model->exists = true;
        $model->id = array_remove($attrs, 'id');
        $model->setBuilder($builder);

        foreach ($attrs as $key => $value) {
            $model->field($key)->fromString($value);
        }

        return $model;
    }

    public function make($attrs = [])
    {
        $model = clone $this;

        return $model->fill($attrs);
    }

    /**
     * Fire event in fields on entity table creation
     *
     * @param Blueprint  $t
     * @param Builder $db
     */
    public function makeTable(Blueprint $t, Builder $query)
    {
        $t->increments('id');
        $this->setBuilder($query);
        foreach ($this->getFields() as $name => $field) {
            if (!in_array($name, $this->localized)) {
                $field->onCreate($t, $query->getQuery()->connection);
            }
        }
    }

    /**
     * Fire event in fields on entity table deletion
     *
     * @param Builder $db
     */
    public function dropTable(Builder $query)
    {
        $this->setBuilder($query);
        foreach ($this->getFields() as $field) {
            $field->onDrop($query->getQuery()->connection);
        }
    }

    /**
     * Fire some event in fields
     *
     * @param string  $event
     * @param Connection $db
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

    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function __call($key, $args)
    {
        return empty($args) ? $this->field($key) : call_user_func_array([$this->field($key), 'output'], $args);
    }

    /**
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        return $this->field($key)->output();
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
        return $this->hasField($key);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->set($key, '');
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
