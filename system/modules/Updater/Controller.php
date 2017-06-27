<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Updater;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/tools/updater', [
            'title' => 'module_updater',
            'url' => '/admin/update',
        ], 20);
    }

    public function index()
    {
        $d = document([
            'content' => 'Current version: '.SYDES_VERSION,
        ]);
        $d->title = t('module_updater');

        return $d;
    }
}
