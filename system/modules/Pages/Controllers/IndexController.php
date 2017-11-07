<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Pages\Controllers;

use Sydes\AdminMenu;

class IndexController
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

        /*
            $schema->create('pages_optional', function (Blueprint $t) {
                $t->increments('id');
                $t->integer('page_id')->unsigned();
                $t->string('key');
                $t->string('value')->nullable();

                $t->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
                $t->unique(['page_id', 'key']);
            });
         */

        /*
         * поле parent сделать внешнм ключом, что бы все потомки удалялись каскадно
         */
    }

    public function view($path)
    {
        return 'qqq'.$path;
    }
}
