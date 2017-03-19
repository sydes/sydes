<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Update;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/update', 'Update@index');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/tools/update', [
            'title' => 'module_update',
            'url' => '/admin/update',
        ], 20);
    }

    public function index()
    {
        $d = document([
            'content' => 'Current version: '.SYDES_VERSION,
        ]);
        $d->title = t('module_update');
        return $d;
    }
}
