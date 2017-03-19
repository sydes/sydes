<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Settings;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/settings', 'Settings@index');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/settings', [
            'title' => 'module_settings',
            'url' => '/admin/settings',
        ], 0);
    }

    public function index()
    {
        $d = document([
            'content' => 'settings list',
        ]);
        return $d;
    }
}
