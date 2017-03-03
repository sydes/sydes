<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Settings;

class Container
{
    private $data = [];
    private $entity;
    private $driver;
    private $changed = false;

    public function __construct($entity, DriverInterface $driver)
    {
        $this->entity = $entity;
        $this->driver = $driver;
        $this->data = $driver->get($entity);
    }

    public function __destruct()
    {
        if ($this->changed) {
            $this->commit();
        }
    }

    /**
     * Sends data to database.
     *
     * @return self
     */
    public function commit()
    {
        $this->driver->set($this->entity, $this->data);
        $this->changed = false;

        return $this;
    }

    /**
     * Gets a value from config or all array.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->data;
        } else {
            return isset($this->data[$key]) ? $this->data[$key] : $default;
        }
    }

    /**
     * Sets a value by key or all array.
     *
     * @param string|array $key
     * @param mixed        $value
     * @return self
     */
    public function set($key, $value = null)
    {
        if (is_null($value)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        $this->changed = true;

        return $this;
    }

    /**
     * Replace old values with new ones
     *
     * @param array $data
     * @return $this
     */
    public function update(array $data)
    {
        $this->data = array_merge($this->data, $data);
        $this->changed = true;

        return $this;
    }

    /**
     * Deletes a value by key or all data.
     *
     * @param string $key
     * @return self
     */
    public function delete($key = null)
    {
        if (is_null($key)) {
            $this->data = [];
        } else {
            unset($this->data[$key]);
        }
        $this->changed = true;

        return $this;
    }
}
