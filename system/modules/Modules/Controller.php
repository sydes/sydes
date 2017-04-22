<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Modules;

use Sydes\AdminMenu;
use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/modules', 'Modules@index');
        $r->post('/admin/module/{name:[a-z-]+}', 'Modules@installModule');
        $r->delete('/admin/module/{name:[a-z-]+}', 'Modules@uninstallModule');
        $r->delete('/admin/module/{name:[a-z-]+}/delete', 'Modules@deleteModule');
        $r->get('/admin/modules/add', 'Modules@add');
        $r->put('/admin/modules/add', 'Modules@upload');
        $r->get('/admin/modules/updates', 'Modules@updates');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addGroup('modules', 'menu_modules', 'th-list', 500)
            ->addItem('modules/constructors', [
                'title' => 'menu_constructors',
                'url' => '#',
            ], 20)
            ->addItem('modules/tools', [
                'title' => 'menu_tools',
                'url' => '#',
            ], 30)
            ->addItem('modules/services', [
                'title' => 'menu_services',
                'url' => '#',
            ], 40);

        $menu->addGroup('system', 'menu_system', 'cog', 600)
            ->addItem('system/modules', [
                'title' => 'module_modules',
                'url' => '/admin/modules',
            ], 10);
    }

    public function index()
    {
        $m = model('Modules');

        $translated = [];
        foreach ($m->all() as $module) {
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
            'content' => view('modules/index', [
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
        removeDir(DIR_MODULE.'/'.studly_case($name));
        notify(t('deleted'));

        return redirect('/admin/modules');
    }

    public function add()
    {
        $d = document([
            'title' => t('module_upload'),
            'content' => view('modules/add'),
            'form_url' => '/admin/modules/add',
            'form_method' => 'PUT',
        ]);

        return $d;
    }

    public function upload()
    {
        $url = app('request')->input('url');
        $file = app('request')->file('file');

        if (!empty($url)) {
            $name = model('Modules')->uploadByUrl($url);
        } elseif ($file->getSize() > 0) {
            $name = model('Modules')->uploadByFile($file);
        } else {
            return back();
        }

        notify(t('uploaded'));

        if (app('request')->has('install')) {
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
