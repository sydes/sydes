<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Import;

class Controller
{
    public static $routes = [
        ['GET', '/admin/import', 'Import@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        app('cmf')->installModule('import');

        app('cmf')->addMenuItem('tools', [
            'title' => 'module_import',
            'url' => '/admin/import',
        ], 300);
    }

	public function uninstall()
    {
        app('cmf')->removeMenuItem('tools', '/admin/import');
        app('cmf')->uninstallModule('import');
    }

    public function index()
    {
        $d = document([
            'content' => 'import/export forms',
        ]);
        return $d;
    }
}
