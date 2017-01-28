<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Page;

class Controller
{
    public static $routes = [
        ['GET', '/admin/pages', 'Page@index'],
    ];

    public function install()
    {
        //echo 'page installed ';
    }

    public function view($path)
    {
        return 'qqq'.$path;
    }
}
