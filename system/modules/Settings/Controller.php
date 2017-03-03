<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Settings;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/settings', 'Settings@index'],
    ];

    public function __construct()
    {

    }

	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('system', [
            'title' => 'module_settings',
            'url' => '/admin/settings',
        ], 10);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('system', '/admin/settings');
    }

    public function index()
    {
        $d = document([
            'content' => 'settings list',
        ]);
        return $d;
    }
}
