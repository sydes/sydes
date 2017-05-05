<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

abstract class Model implements \ArrayAccess
{
    protected $connection = 'site';
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected $fillable = [];

    public function __construct(array $attrs = [])
    {
        $this->fill($attrs);
    }

    public function fill(array $attrs)
    {
        foreach ($this->fillableFromArray($attrs) as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    protected function fillableFromArray(array $attrs)
    {
        if (count($this->fillable) > 0) {
            return array_intersect_key($attrs, array_flip($this->fillable));
        }

        return $attrs;
    }

    public function offsetExists($key)
    {
        return !empty($this->attributes[$key]);
    }

    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetUnset($key)
    {
        $this->attributes[$key] = '';
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        } else {
            $name = get_class($this);

            return snake_case(($pos = strrpos($name, '\\')) ? substr($name, $pos + 1) : $name).'s';
        }
    }
}
