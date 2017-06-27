<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Import;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/tools/import', [
            'title' => 'module_import',
            'url' => '/admin/import',
        ], 10);
    }

    public function index()
    {
        $d = document([
            'content' => 'import/export forms',
        ]);
        return $d;
    }
}
