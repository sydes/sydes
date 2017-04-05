<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Theme;

use App\AdminMenu;
use App\Theme;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->addGroup('/admin/theme', function (Route $r) {
            $r->get('s', 'Theme@index');

            $r->get('/{name:[a-z-]+}', 'Theme@view');
            $r->post('/{name:[a-z-]+}/activate', 'Theme@activate');
            $r->delete('/{name:[a-z-]+}', 'Theme@deleteTheme');

            $r->get('/layout/{name:[a-z-]+}', 'Theme@viewLayout');
            $r->post('/layout/{name:[a-z-]+}', 'Theme@saveLayout');
        });
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/themes', [
            'title' => 'module_theme',
            'url' => '/admin/themes',
        ], 20);
    }

    public function index()
    {
        $themes = model('Theme')->all();

        $name = app('site')->get('theme');
        $current = $themes[$name];
        unset($themes[$name]);

        $content = view('theme/index', ['themes' => $themes, 'current' => $current]);

        $d = document([
            'title' => t('module_theme'),
            'header_actions' => \H::a(t('add_theme'), '/admin/theme/add', ['class' => 'btn btn-primary']),
            'content' => $content,
        ]);
        $d->addCss('module-theme', assetsDir('theme').'/css/style.css');

        return $d;
    }

    public function view($name)
    {
        return $name;
    }

    public function activate($name)
    {
        model('Theme')->activate($name);

        return back();
    }

    public function deleteTheme($name)
    {
        if (app('site')->get('theme') == $name) {
            alert(t('no').'!');

            return back();
        }

        $theme = new Theme($name);
        $theme->delete();

        notify(t('deleted'));

        return redirect('/admin/themes');
    }
}
