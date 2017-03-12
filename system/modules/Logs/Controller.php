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
        $menu->addItem('system', [
            'title' => 'module_logs',
            'url' => '/admin/logs',
        ], 500);
    }

    public function uninstall(AdminMenu $menu)
    {
        $menu->removeItem('system', '/admin/logs');
    }

    public function index()
    {
        $d = document([
            'content' => 'All logs',
        ]);
        return $d;
    }
}
