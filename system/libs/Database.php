<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Database
{
    /** @var PDO[] */
    protected $connections = [];
    protected $config;

    public function __construct()
    {
        $this->config = include DIR_CONFIG.'/database.php';
    }

    public function connection($name = null)
    {
        if (!$name) {
            $name = $this->config['default'];
        }

        if (!isset($this->connections[$name])) {
            $current = $this->config['connections'][$name];

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
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->connections[$name] = new PDO($dsn, $user, $pass, $opt);
        }

        return $this->connections[$name];
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->connection(), $name), $args);
    }
}
