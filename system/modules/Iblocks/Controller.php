<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Iblocks;

class Controller
{
    public static $routes = [
        ['GET', '/admin/iblocks', 'Iblocks@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        app('cmf')->installModule('iblocks');

        app('cmf')->addMenuItem('system', [
            'title' => 'module_iblocks',
            'url' => '/admin/iblocks',
        ], 300);
    }

	public function uninstall()
    {
        app('cmf')->removeMenuItem('system', '/admin/iblocks');
        app('cmf')->uninstallModule('iblocks');
    }

    public function index()
    {
        $d = document([
            'content' => 'list of registered, user and theme iblocks',
        ]);
        $d->title = 'Index page of module';
        return $d;
    }
}
