<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Config
{

    /** @var array */
    private $data = [];

    /** @var string */
    private $extension;

    /** @var \PDO object */
    private $db;

    /** @var bool */
    private $changed = false;

    public function __construct($extension, Database $db)
    {
        $this->extension = $extension;
        $this->db = $db;

        $stmt = $this->db->query("SELECT key, value FROM config WHERE extension = '{$extension}'");
        $data = $stmt->fetchAll();
        if ($data) {
            foreach ($data as $d) {
                $this->data[$d['key']] = json_decode($d['value'], true);
            }
        }
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
        $this->db->exec("DELETE FROM config WHERE extension = '{$this->extension}'");
        $stmt = $this->db->prepare("INSERT INTO config (module, key, value) VALUES ('{$this->extension}', :key, :value)");
        foreach ($this->data as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => json_encode($value)]);
        }
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
