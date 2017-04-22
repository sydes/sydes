<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Database
{
    /** @var \PDO */
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
        if (!(bool)$this->db->query("SELECT 1 FROM {$table} WHERE 1")) {
            $this->db->exec($scheme);
        }
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->db, $name), $args);
    }
}
