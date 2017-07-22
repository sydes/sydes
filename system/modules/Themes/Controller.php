<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/themes', [
            'title' => 'module_themes',
            'url' => '/admin/themes',
        ], 20);
    }

    public function index()
    {
        $themes = model('Themes')->getAll();

        $name = app('site')->get('theme');
        $current = $themes[$name];
        unset($themes[$name]);

        $d = document([
            'title' => t('module_themes'),
            'header_actions' => \H::a(t('add_theme'), '/admin/themes/add', ['button' => 'primary']),
            'content' => view('themes/list', ['themes' => $themes, 'current' => $current]),
        ]);
        $d->addCss('module-theme', 'themes:css/style.css');

        return $d;
    }

    public function view($name)
    {
        return $name;
    }

    public function activate($name)
    {
        model('Themes')->activate($name);

        return back();
    }

    public function delete($name)
    {
        if (app('site')->get('theme') == $name) {
            return back();
        }

        model('Themes')->delete($name);
        notify(t('deleted'));

        return redirect('/admin/themes');
    }
}
