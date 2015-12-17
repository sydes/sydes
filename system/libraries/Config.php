<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
class Config {

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var string
     */
    private $module;

    /**
     * @var PDO object
     */
    private $db;

    /**
     * @var bool
     */
    private $changed = false;

    public function __construct($module, Database $db) {
        $this->module = $module;
        $this->db = $db;

        /* TODO таки не стоит
          $this->db->issetTable('config', "CREATE TABLE config (
          id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
          module TEXT,
          key TEXT,
          value TEXT,
          UNIQUE (module,key)
          )"); */

        $stmt = $this->db->query("SELECT key, value FROM config WHERE module = '{$module}'");
        $data = $stmt->fetchAll();
        if ($data) {
            foreach ($data as $d) {
                $this->data[$d['key']] = json_decode($d['value'], true);
            }
        }
    }

    public function __destruct() {
        if ($this->changed) {
            $this->commit();
        }
    }

    /**
     * Gets a value from config or all array.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key = null, $default = null) {
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
    public function set($key, $value = null) {
        if (is_null($value)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        $this->changed = true;
        return $this;
    }

    /**
     * Deletes a value by key or all data.
     *
     * @param string $key
     * @return self
     */
    public function delete($key = null) {
        if (is_null($key)) {
            $this->data = array();
        } else {
            unset($this->data[$key]);
        }
        $this->changed = true;
        return $this;
    }

    /**
     * Sends data to database.
     *
     * @return self
     */
    public function commit() {
        $this->db->exec("DELETE FROM config WHERE module = '{$this->module}'");
        $stmt = $this->db->prepare("INSERT INTO config (module, key, value) VALUES ('{$this->module}', :key, :value)");
        foreach ($this->data as $key => $value) {
            $stmt->execute(array('key' => $key, 'value' => json_encode($value)));
        }
        $this->changed = false;
        return $this;
    }
}
