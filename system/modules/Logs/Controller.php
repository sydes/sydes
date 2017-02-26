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

	public function install($cmf)
    {
        $cmf->installModule('logs');

        $cmf->addMenuItem('system', [
            'title' => 'module_logs',
            'url' => '/admin/logs',
        ], 500);
    }

	public function uninstall($cmf)
    {
        $cmf->removeMenuItem('system', '/admin/logs');
        $cmf->uninstallModule('logs');
    }

    public function index()
    {
        $d = document([
            'content' => 'All logs',
        ]);
        return $d;
    }
}
