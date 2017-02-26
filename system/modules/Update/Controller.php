<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Update;

class Controller
{
    public static $routes = [
        ['GET', '/admin/update', 'Update@index'],
    ];

	public function install()
    {
        app('cmf')->installModule('update', [
            'handlers' => ['Module\Update\Handlers::init'],
        ]);

        app('cmf')->addMenuItem('tools', [
            'title' => 'module_update',
            'url' => '/admin/update',
        ], 240);
    }

	public function uninstall()
    {
        app('cmf')->removeMenuItem('tools', '/admin/themes');
        app('cmf')->uninstallModule('update');
    }

    public function index()
    {
        $d = document([
            'content' => 'Current version: '.SYDES_VERSION,
        ]);
        $d->title = t('module_update');
        return $d;
    }
}
