<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

abstract class Entity implements EntityInterface
{
    protected $table;
    protected $primaryKey = 'id';
    protected $timestamps = true;
    protected $eav = false;
    protected $localized = [];
    protected $fields = [];
    protected $props = [];
    protected $_fields = [];
    protected $data = [];
    protected $inited = false;

    /**
     * Entity constructor.
     *
     * @param array $attrs
     */
    public function __construct(array $attrs = [])
    {
        $this->props = [
            $this->primaryKey => 1,
            'status' => 1,
            'created_at' => 1,
            'updated_at' => 1,
        ];

        $this->fill($attrs);
    }

    /**
     * {@inheritDoc}
     */
    public static function create(array $attrs)
    {
        $me = new static;

        return $me->fill($attrs);
    }

    /**
     * {@inheritDoc}
     */
    public function fill(array $attrs)
    {
        foreach (array_intersect_key($attrs, $this->fields + $this->props) as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function allFields()
    {
        if (!$this->inited) {
            foreach ($this->fields + $this->props as $name) {
                $this->field($name);
            }
            $this->inited = true;
        }

        return $this->_fields;
    }

    /**
     * {@inheritDoc}
     */
    public function field($name)
    {
        if (!isset($this->_fields[$name])) {
            $this->_fields[$name] = $this->initField($name);
        }

        return $this->_fields[$name];
    }

    /**
     * {@inheritDoc}
     */
    protected function initField($name)
    {
        if (isset($this->fields[$name])) {
            $field = $this->fields[$name];
        } elseif (isset($this->props[$name])) {
            $field = [
                'type' => 'Hidden',
                'settings' => [],
            ];
        } else {
            throw new \InvalidArgumentException('Field '.$name.' not found in '.get_class($this));
        }

        $class = app('form.fields')[$field['type']];

        return new $class($name, $this->data[$name], $field['settings']);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getTable()
    {
        if (!$this->table) {
            $name = get_class($this);

            return snake_case(($pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : $name).'s';
        }

        return $this->table;
    }

    /**
     * {@inheritDoc}
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * {@inheritDoc}
     */
    public function useTimestamps($val = true)
    {
        $this->timestamps = $val;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->_fields[$this->getKeyName()];
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        $name = get_class($this);

        return snake_case(($pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : $name).'_'.$this->primaryKey;
    }

    /**
     * {@inheritDoc}
     */
    public function usesEav()
    {
        return $this->eav;
    }

    /**
     * {@inheritDoc}
     */
    public function useEav($val = true)
    {
        $this->eav = $val;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasLocalized()
    {
        return !empty($this->localized);
    }

    /**
     * {@inheritDoc}
     */
    public function getLocalized()
    {
        return $this->localized;
    }

    /**
     * {@inheritDoc}
     */
    public function setLocalized(array $fields = [])
    {
        $this->localized = $fields;

        return $this;
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
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_fields);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->data[$key] = '';
        $this->field($key)->fromString('');
    }

    /**
     * {@inheritDoc}
     */
    public function makeTable()
    {
        $cols = [];
        foreach ($this->allFields() as $field) {
            $cols = $field->onCreate($cols);
        }

        return $cols;
    }

    /**
     * {@inheritDoc}
     */
    public function dropTable()
    {
        foreach ($this->allFields() as $field) {
            $field->onDrop();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave()
    {
        foreach ($this->allFields() as $field) {
            if ($field->beforeSave() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function afterSave()
    {
        foreach ($this->allFields() as $field) {
            $field->afterSave();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDelete()
    {
        foreach ($this->allFields() as $field) {
            if ($field->beforeDelete() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function afterDelete()
    {
        foreach ($this->allFields() as $field) {
            $field->afterDelete();
        }
    }
}
