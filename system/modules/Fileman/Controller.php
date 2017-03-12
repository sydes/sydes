<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fileman;

use App\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules', [
            'title' => 'module_fileman',
            'url' => '#fileman',
        ], 10);
    }

    public function uninstall(AdminMenu $menu)
    {
        $menu->removeItem('modules', '#fileman');
    }
}
