<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Model
{
    /** @var \PDO */
    protected $db;
    public function __construct()
    {
        $this->db = app('db');
    }
}
