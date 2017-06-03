<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Route;

class Controller
{
    public function install()
    {
        app('db')->run("CREATE TABLE routes (
    alias TEXT NOT NULL,
    route TEXT NOT NULL,
    params TEXT,
    UNIQUE (alias,route)
);");
    }

    public function uninstall()
    {
        app('db')->exec("DROP TABLE IF EXISTS routes");
    }
}
