<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Pages;

class Controller
{
    public static $routes = [
        ['GET', '/admin/pages', 'Pages@index'],
    ];

    public function install($cmf)
    {
        $cmf->installModule('pages');
    }

    public function uninstall($cmf)
    {
        $cmf->uninstallModule('pages');
    }

    public function index()
    {
        return 'page index or tree';
    }

    public function view($path)
    {
        return 'qqq'.$path;
    }
}
