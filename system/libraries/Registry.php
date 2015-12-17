<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Registry {

    protected $registry;
    private static $instance;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Reuse the instance created by $ctor
     *
     * @param Closure $ctor
     * @return Closure
     */
    public function reuse(Closure $ctor) {
        return function($self) use ($ctor) {
            static $instance = null;
            return null === $instance ? $instance = $ctor($self) : $instance;
        };
    }

    /**
     * Register a value
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->registry[$name] = $value;
    }

    /**
     * Get the depencency by its name
     *
     * @param $name
     * @return mixed
     * @throws BaseException
     */
    public function __get($name) {
        if (!array_key_exists($name, $this->registry)) {
            throw new BaseException(sprintf(t('error_undefined_dependency'), $name));
        }
        return $this->registry[$name] instanceof Closure ? $this->registry[$name]($this) : $this->registry[$name];
    }

    /**
     * Call the ctor function for $name dependency with $arguments
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call($name, array $arguments) {
        if (!array_key_exists($name, $this->registry)) {
            throw new BaseException(sprintf(t('error_undefined_dependency'), $name));
        }
        if (!$this->registry[$name] instanceof Closure) {
            throw new BaseException(sprintf(t('error_dependency_is_not_constructable'), $name));
        }
        array_unshift($arguments, $this);
        return call_user_func_array($this->registry[$name], $arguments);
    }

    /**
     * Get the depencency by its name
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws BaseException
     */
    static public function __callStatic($name, array $arguments) {
        if (!array_key_exists($name, self::$instance->registry)) {
            throw new BaseException(sprintf(t('error_undefined_dependency'), $name));
        }
        return self::$instance->registry[$name];
    }

    /**
     * Returns true if $name is set
     *
     * @param $name
     * @return bool
     */
    public function __isset($name) {
        return array_key_exists($name, $this->registry);
    }
}
