<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Import;

use App\Cmf;

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
        Cmf::installModule('import');

        Cmf::addMenuItem('tools', [
            'title' => 'module_import',
            'url' => '/admin/import',
        ], 300);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('tools', '/admin/import');
        Cmf::uninstallModule('import');
    }

    public function index()
    {
        $d = document([
            'content' => 'import/export forms',
        ]);
        return $d;
    }
}
