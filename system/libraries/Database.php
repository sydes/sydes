<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App;

class Database
{

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Connects to database of specified site
     *
     * @param string $site site id
     */
    public function __construct($site)
    {
        if (empty($site)) {
            return;
        }

        $this->db = new \PDO(
            'sqlite:'.DIR_SITE.'/'.$site.'/database.db', null, null, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );

        $this->db->exec('SET NAMES "utf8"');
        $this->db->exec('SET time_zone = "'.date_default_timezone_get().'"');
    }

    /**
     * Checks for the existence the table and create it if have not
     *
     * @param string $table  table name
     * @param string $scheme scheme of table
     */
    public function issetTable($table, $scheme)
    {
        /* TODO в вечный кеш инфу о созданных таблицах кидать */
        if (!(bool)$this->query("SELECT 1 FROM {$table} WHERE 1")) {
            $this->exec($scheme);
        }
    }

    public function __call($name, array $args)
    {
        return $this->db->$name($args);
    }

}
