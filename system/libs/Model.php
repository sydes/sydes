<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Model
{
    /** @var \PDO */
    protected $db;
    public function __construct()
    {
        $this->db = app('db');
    }
}
