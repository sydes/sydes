<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Iblocks;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/iblocks', 'Iblocks@index'],
    ];

    public function __construct()
    {

    }

	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('system', [
            'title' => 'module_iblocks',
            'url' => '/admin/iblocks',
        ], 300);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('system', '/admin/iblocks');
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
