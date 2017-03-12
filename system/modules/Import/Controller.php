<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Import;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/import', 'Import@index');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addSubItem('modules', '#tools', [
            'title' => 'module_import',
            'url' => '/admin/import',
        ], 300);
    }

    public function uninstall(AdminMenu $menu)
    {
        $menu->removeSubItem('modules', '#tools', '/admin/import');
    }

    public function index()
    {
        $d = document([
            'content' => 'import/export forms',
        ]);
        return $d;
    }
}
