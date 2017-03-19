<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Iblocks;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/iblocks', 'Iblocks@index');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/iblocks', [
            'title' => 'module_iblocks',
            'url' => '/admin/iblocks',
        ], 30);
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
