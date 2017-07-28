<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Api\Concerns;

use Module\Entity\Api\FieldInterface;
use Module\Entity\Plugins\Fields\EntityRelationField;

trait HasFields
{
    protected $changed = [];
    protected $bootedFields = [];
    protected $booted = false;

    /**
     * Boot all fields and return them
     *
     * @param array $keys
     * @return FieldInterface[]
     */
    public function getFields(array $keys = [])
    {
        if (!$this->booted) {
            $this->booted = true;

            foreach ($this->fields as $name => $void) {
                $this->field($name);
            }

            // sort fields by weight even if some was booted previously
            uasort($this->fields, 'sortByWeight');
            $this->bootedFields = array_replace($this->fields, $this->bootedFields);
        }

        if (empty($keys)) {
            return $this->bootedFields;
        }

        $keys = array_flip($keys);

        return array_intersect_key(array_replace($keys, $this->bootedFields), $keys);
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
        $field = new $class($name, null, $field['settings']);

        // fields with relations should know about entity
        if ($field instanceof EntityRelationField) {
            $field->init($this);
        }

        return $field;
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
     * @param string $key
     * @return bool
     */
    public function hasField($key)
    {
        return isset($this->fields[$key]);
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param array|string|null $attrs
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
     * @param array|string|null $attrs
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
}
