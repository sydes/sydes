<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Iblock;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/iblock', 'Iblock@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        Cmf::installModule('iblock');

        Cmf::addMenuItem('system', [
            'title' => 'module_iblock',
            'url' => '/admin/iblocks',
        ], 300);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('system', '/admin/iblocks');
        Cmf::uninstallModule('iblock');
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
