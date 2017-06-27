<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Pages;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addGroup('pages', [
            'title' => 'module_pages',
            'icon' => 'file'
        ], 0);
        $menu->addItem('pages/news', [
            'title' => 'News',
            'url' => '/admin/pages/news',
            'quick_add' => true,
        ], 10);
    }

    public function index()
    {
        return 'page index or tree';
    }

    public function view($path)
    {
        return 'qqq'.$path;
    }
}
