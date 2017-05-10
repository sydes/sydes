<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

abstract class Dao
{
    /** @var PDO */
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function setDb(Database $db) {
        $this->db = $db;
    }

    public function getDb() {
        return $this->db;
    }
}
