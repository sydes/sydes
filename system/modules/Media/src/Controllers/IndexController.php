<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Media\Controllers;

use Sydes\AdminMenu;

class IndexController
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/media', [
            'title' => 'module_media',
        ], 0);
    }
}
