<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Logs;

use App\Cmf;

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
        Cmf::installModule('logs');

        Cmf::addMenuItem('system', [
            'title' => 'module_logs',
            'url' => '/admin/logs',
        ], 500);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('system', '/admin/logs');
        Cmf::uninstallModule('logs');
    }

    public function index()
    {
        $d = document([
            'content' => 'All logs',
        ]);
        return $d;
    }
}
