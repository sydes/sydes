<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Forms\Controllers;

use Sydes\AdminMenu;

class IndexController
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/constructors/forms', [
            'title' => 'module_forms',
            'url' => '/admin/forms',
        ], 10);
    }

    public function index()
    {
        $d = document([
            'content' => 'forms index',
        ]);
        return $d;
    }
}
