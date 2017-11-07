<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Media;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/media', [
            'title' => 'module_media',
        ], 0);
    }
}
