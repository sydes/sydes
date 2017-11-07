<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Modules\Controllers;

use Sydes\AdminMenu;
use Sydes\Contracts\Http\Request;

class IndexController
{
    public function install(AdminMenu $menu)
    {
        $menu->addGroup('modules', [
            'title' => 'menu_modules',
            'icon' => 'th-list'
        ], 500)
            ->addItem('modules/constructors', [
                'title' => 'menu_constructors',
            ], 20)
            ->addItem('modules/tools', [
                'title' => 'menu_tools',
            ], 30)
            ->addItem('modules/services', [
                'title' => 'menu_services',
            ], 40);

        $menu->addGroup('system', [
            'title' => 'menu_system',
            'icon' => 'cog'
        ], 600)
            ->addItem('system/modules', [
                'title' => 'module_modules',
                'url' => '/admin/modules',
            ], 10);
    }

    public function index()
    {
        $m = model('Modules');

        $translated = [];
        foreach ($m->getAll() as $module) {
            app('translator')->loadFrom('module', $module);

            $mod = snake_case($module, '-');
            $name = t('module_'.$mod);
            $description = t('module_'.$mod.'_description');
            $translated[$mod] = [
                'name' => $name != 'module_'.$mod ? $name : false,
                'description' => $description != 'module_'.$mod.'_description' ? $description : false,
            ];
        }

        $d = document([
            'title' => t('module_modules'),
            'header_actions' => \H::a(t('add_module'), '/admin/modules/add', ['button' => 'primary']),
            'content' => view('modules/list', [
                'installed' => $m->getList('installed'),
                'uploaded' => $m->getList('uninstalled'),
                'default' => $m->getList('default'),
                'translated' => $translated,
            ]),
        ]);

        return $d;
    }

    public function installModule($name)
    {
        model('Modules')->install($name);
        notify(t('installed'));

        return back();
    }

    public function uninstallModule($name)
    {
        model('Modules')->uninstall($name);
        notify(t('uninstalled'));

        return redirect('/admin/modules');
    }

    public function deleteModule($name)
    {
        removeDir(app('dir.module').'/'.studly_case($name));
        notify(t('deleted'));

        return redirect('/admin/modules');
    }

    public function add()
    {
        $d = document([
            'title' => t('module_upload'),
            'content' => view('modules/add', [
                'options' => [
                    'method' => 'post',
                    'url' => '/admin/modules/add',
                    'form' => 'main',
                    'files' => true,
                ],
            ]),
        ]);

        return $d;
    }

    public function upload(Request $req)
    {
        $url = $req->input('url');
        $file = $req->file('file');

        if (!empty($url)) {
            $name = model('Modules')->uploadByUrl($url);
        } elseif ($file->getSize() > 0) {
            $name = model('Modules')->uploadByFile($file);
        } else {
            return back();
        }

        notify(t('uploaded'));

        if ($req->has('install')) {
            model('Modules')->install($name);
            notify(t('installed'));
        }

        return back();
    }

    public function updates()
    {
        alert(p('modules_for_update', 5), 'info');

        return back();
    }
}
