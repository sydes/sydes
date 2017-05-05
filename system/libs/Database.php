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

    public function __construct($connection = null)
    {
        $config = include DIR_CONFIG.'/database.php';

        $current = $config['connections'][$connection ?: $config['default']];

        if ($current['driver'] == 'sqlite') {
            $dsn = 'sqlite:'.$current['database'];
            $user = null;
            $pass = null;
        } elseif ($current['driver'] == 'mysql') {
            $dsn = 'mysql:host='.$current['host'].';dbname='.$current['database'].';charset='.$current['charset'];
            $user = $current['username'];
            $pass = $current['password'];
        }

        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];

        $this->db = new \PDO($dsn, $user, $pass, $opt);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->db, $name), $args);
    }
}
