<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Cache {

    /**
     * The file cache directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * Create a new file cache instance.
     *
     * @param  string  $directory
     */
    public function __construct($directory) {
        $this->directory = $directory;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null) {
        $path = $this->path($key);

        if (!file_exists($path)) {
            return $default;
        }

        $contents = file_get_contents($path);
        $expire = substr($contents, 0, 10);

        if ($expire < time()) {
            $this->forget($key);
            return $default;
        }

        return unserialize(substr($contents, 10));
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $seconds
     */
    public function put($key, $value, $seconds = 2678400) {
        $value = $this->expiration($seconds) . serialize($value);

        $path = $this->path($key);

        file_put_contents($path, $value, LOCK_EX);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function forever($key, $value) {
        $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key) {
        $file = $this->path($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    /**
     * Remove all items from the cache.
     */
    public function flush() {
        if (file_exists($this->directory)) {
            foreach (glob($this->directory . '/*') as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  int     $seconds
     * @param  Closure $callback
     * @return mixed
     */
    public function remember($key, Closure $callback, $seconds = 2678400) {
        if (!is_null($value = $this->get($key))) {
            return $value;
        }

        $this->put($key, $value = $callback(), $seconds);

        return $value;
    }

    /**
     * Get the full path for the given cache key.
     *
     * @param  string $key
     * @return string
     */
    protected function path($key) {
        return $this->directory . '/' . md5($key) . '.cache';
    }

    /**
     * Get the expiration time based on the given seconds.
     *
     * @param  int $seconds
     * @return int
     */
    protected function expiration($seconds) {
        if ($seconds === 0) {
            return 9999999999;
        }
        return time() + $seconds;
    }
}
