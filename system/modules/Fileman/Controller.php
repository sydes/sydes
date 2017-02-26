<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fileman;

class Controller
{
    public static $routes = [
        ['GET', '/admin/fileman', 'Fileman@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        app('cmf')->installModule('fileman');

        app('cmf')->addMenuItem('tools', [
            'title' => 'module_fileman',
            'url' => '#',
        ], 10);
    }

	public function uninstall()
    {
        app('cmf')->removeMenuItem('tools', '#');
        app('cmf')->uninstallModule('fileman');
    }

    public function index()
    {
        $d = document([
            'content' => 'fileman settings',
        ]);
        return $d;
    }
}
