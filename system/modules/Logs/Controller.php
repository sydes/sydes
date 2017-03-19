<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Logs;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/logs', 'Logs@index');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/logs', [
            'title' => 'module_logs',
            'url' => '/admin/logs',
        ], 100);
    }

    public function index()
    {
        $d = document([
            'content' => 'All logs',
        ]);
        return $d;
    }
}
