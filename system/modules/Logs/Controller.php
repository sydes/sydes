<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Logs;

class Controller
{
    public static $routes = [
        ['GET', '/admin/logs', 'Logs@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        app('cmf')->installModule('logs');

        app('cmf')->addMenuItem('system', [
            'title' => 'module_logs',
            'url' => '/admin/logs',
        ], 500);
    }

	public function uninstall()
    {
        app('cmf')->removeMenuItem('system', '/admin/logs');
        app('cmf')->uninstallModule('logs');
    }

    public function index()
    {
        $d = document([
            'content' => 'All logs',
        ]);
        return $d;
    }
}
