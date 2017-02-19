<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Theme;

use App\Cmf;
use App\Theme;

class Controller
{
    public static $routes = [
        ['GET', '/admin/themes', 'Theme@index'],
        ['GET', '/admin/theme/{name:[a-z-]+}', 'Theme@view'],
        ['GET', '/admin/theme/layout/{name:[a-z-]+}', 'Theme@viewLayout'],
        ['POST', '/admin/theme/layout', 'Theme@saveLayout'],
        ['POST', '/admin/theme/{name:[a-z-]+}/activate', 'Theme@activate'],
        ['POST', '/admin/theme/{name:[a-z-]+}/delete', 'Theme@deleteTheme'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        Cmf::installModule('theme');

        Cmf::addMenuItem('system', [
            'title' => 'module_theme',
            'url' => '/admin/themes',
        ], 200);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('system', '/admin/themes');
        Cmf::uninstallModule('theme');
    }

    public function index()
    {
        $themes = model('theme')->all();

        $name = app('site')['theme'];
        $current = $themes[$name];
        unset($themes[$name]);

        $content = view('theme/index', ['themes' => $themes, 'current' => $current]);

        $d = document([
            'content' => $content,
        ]);
        $d->addCss('module-theme', assetsDir('theme').'/css/style.css');
        $d->title = t('module_theme');
        return $d;
    }

    public function view($name)
    {
        return $name;
    }

    public function activate($name)
    {
        $config = app('rawSiteConfig');
        $config['theme'] = $name;
        Cmf::saveSiteConfig($config);
        return back();
    }

    public function deleteTheme($name)
    {
        if (app('site')['theme'] == $name) {
            alert(t('no').'!');
            return back();
        }

        if (!actionConfirmed()) {
            return confirmAction(sprintf(t('confirm_theme_deletion'), $name), '/admin/themes');
        }

        $theme = new Theme($name);
        $theme->delete();

        notify(t('deleted'));
        return redirect('/admin/themes');
    }
}
