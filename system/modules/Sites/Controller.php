<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Sites;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/sites', 'Sites@index'],
        ['GET', '/admin/sites/{id:\d+}/edit', 'Sites@edit'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        Cmf::installModule('sites');

        Cmf::addMenuItem('system', [
            'title' => 'module_sites',
            'url' => '/admin/sites',
        ], 10);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('system', '/admin/sites');
        Cmf::uninstallModule('sites');
    }

    public function index()
    {
        $d = document([
            'content' => 'list of sites',
        ]);
        return $d;
    }

    public function edit($id)
    {
        $d = document([
            'content' => 'site s'.$id.' editor',
        ]);
        return $d;
    }
}
