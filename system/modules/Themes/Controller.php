<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->addGroup('/admin/theme', function (Route $r) {
            $r->get('s', 'Themes@index');
            $r->get('s/add', 'Themes@add');

            $r->get('/{name:[a-z-]+}', 'Themes@view');
            $r->post('/{name:[a-z-]+}', 'Themes@activate');
            $r->delete('/{name:[a-z-]+}', 'Themes@delete');

            $r->get('/layout/{name:[a-z-]+}', 'Themes/Layouts@edit');
            $r->post('/layout/{name:[a-z-]+}', 'Themes/Layouts@save');
        });
    }

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
            'title' => t('module_theme'),
            'header_actions' => \H::a(t('add_theme'), '/admin/themes/add', ['class' => 'btn btn-primary']),
            'content' => view('themes/index', ['themes' => $themes, 'current' => $current]),
        ]);
        $d->addCss('module-theme', assetsDir('themes').'/css/style.css');

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
