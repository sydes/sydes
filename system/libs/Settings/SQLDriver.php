<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Settings;

use App\Database;

class SQLDriver implements DriverInterface
{
    /** @var \PDO */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function get($ext)
    {
        $ret = [];
        $stmt = $this->db->query("SELECT key, value FROM settings WHERE extension = '{$ext}'");
        $data = $stmt->fetchAll();
        foreach ($data as $d) {
            $ret[$d['key']] = json_decode($d['value'], true);
        }

        return $ret;
    }

    public function set($ext, $data)
    {
        $this->db->exec("DELETE FROM settings WHERE extension = '{$ext}'");
        $stmt = $this->db->prepare("INSERT INTO settings (extension, key, value) VALUES ('{$ext}', :key, :value)");
        foreach ($data as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => json_encode($value)]);
        }
    }
}
