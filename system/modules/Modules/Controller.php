<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Modules;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/modules', 'Modules@index');
        $r->post('/admin/module/{name:[a-z-]+}/install', 'Modules@installModule');
        $r->post('/admin/module/{name:[a-z-]+}/uninstall', 'Modules@uninstallModule');
        $r->get('/admin/modules/add', 'Modules@add');
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
        $m = model('modules');

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
            'header_actions' =>
                \H::a(t('check_updates'), '/admin/modules/updates', ['class' => 'btn btn-primary']).' '.
                \H::a(t('upload_module'), '/admin/modules/add', ['class' => 'btn btn-primary']),
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
        model('modules')->install($name);
        notify(t('installed'));

        return back();
    }

    public function uninstallModule($name)
    {
        model('modules')->uninstall($name);
        notify(t('uninstalled'));

        return back();
    }

    public function add()
    {
        $d = document([
            'title' => 'Загрузка модуля',
            'content' => 'форма загрузки, поле для ссылки на архив, браузер расширений',
        ]);

        return $d;
    }

    public function updates()
    {
        alert(p('modules_for_update', 5), 'info');

        return back();
    }
}
