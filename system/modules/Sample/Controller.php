<?php
/*
 * Sample module
 */

namespace Module\Sample;

use App\Cmf;

class Controller
{

    public function __construct()
    {

    }

	public function install()
    {
        /* создать таблицы */

         Cmf::installModule('sample', [
            'handlers' => ['Module\\Sample\\Handlers'],
            'files' => ['functions.php'],
         ]);

        Cmf::addRoutes('sample', [
            ['GET', '/admin/sample', 'Sample@index'],
            ['GET', '/admin/sample/another', 'Sample@myMethod'],
            ['GET', '/sample-not-menu', 'Sample@notInMenu']
        ]);

        Cmf::addMenuGroup('sample', 'menu_sample', 'star', 120);
        Cmf::addMenuItem('sample', [
            'title' => 'sample_page',
            'url' => '/admin/sample',
            'quick_add' => true,
        ], 10);
        Cmf::addMenuItem('sample', [
            'title' => 'another_page',
            'url' => '/admin/sample/another'
        ], 20);
    }

	public function uninstall()
    {
        Cmf::removeRoutes('sample');
        Cmf::removeMenuGroup('sample');
        Cmf::uninstallModule('sample');

        /* удалить таблицы и конфиги */
    }

    public function index()
    {
        $d = document([
            'content' => 'content for <strong>/admin/sample</strong><br><a href="/sample-not-menu">strange link</a>',
        ]);
        $d->title = 'Index page of Sample module';
        return $d;
    }

    public function myMethod()
    {
        $d = document([
            'content' => 'content for <strong>/admin/sample/another</strong>',
        ]);
        $d->title = 'Another page';
        return $d;
    }

    public function notInMenu()
    {
        $d = document([
            'content' => 'hello there! <a href="/admin/sample">back</a>',
        ]);
        $d->title = 'Strange page';
        return $d;
    }
}
