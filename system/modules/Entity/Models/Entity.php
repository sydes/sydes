<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Models;

use Module\Fields\Plugin\Fields\FieldInterface;

abstract class Entity
{
    protected $connection = 'site';
    protected $table;
    protected $primaryKey = 'id';
    protected $timestamps = true;
    protected $eav = false;
    protected $localized = [];
    protected $fields = [];
    protected $attributes = [];
    protected $data = [];

    /**
     * Entity constructor.
     *
     * @param array $attrs
     */
    public function __construct(array $attrs = [])
    {
        $this->fill($attrs);
    }

    /**
     * @param array $attrs
     * @return $this
     */
    public static function create(array $attrs)
    {
        return new static($attrs);
    }

    /**
     * @param array $attrs
     * @return $this
     */
    public function fill(array $attrs)
    {
        foreach (array_intersect_key($attrs, $this->fields) as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param array $props
     * @return $this
     */
    public function withProps(array $props)
    {
        foreach (array_intersect_key($props, [
            $this->primaryKey => 1,
            'status' => 1,
            'created_at' => 1,
            'updated_at' => 1,
        ]) as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return FieldInterface
     */
    public function getField($name)
    {
        if (!isset($this->attributes[$name])) {
            $this->initField($name);
        }

        return $this->attributes[$name];
    }

    /**
     * @param string $name
     */
    protected function initField($name)
    {
        $class = app('formFields')[$this->fields[$name]['type']];
        $this->attributes[$name] = new $class($name, $this->data[$name], $this->fields[$name]['settings']);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        } else {
            $name = get_class($this);

            return snake_case(($pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : $name).'s';
        }
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $name
     */
    public function setConnection($name)
    {
        $this->connection = $name;
    }

    /**
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * @return string
     */
    public function getPk()
    {
        return $this->primaryKey;
    }

    /**
     * @return bool
     */
    public function usesEav()
    {
        return $this->eav;
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
     * @param string $key
     * @return FieldInterface
     */
    public function __get($key)
    {
        return $this->getField($key)->render();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->getField($key)->set($value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->getField($key)->set('');
    }
}
