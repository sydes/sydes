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

class Controller
{
    public static $routes = [
        ['GET', '/admin/themes', 'Theme@index'],
        ['GET', '/admin/theme/{name:[a-z-]+}', 'Theme@view'],
        ['GET', '/admin/theme/layout/{name:[a-z-]+}', 'Theme@viewLayout'],
        ['POST', '/admin/theme/layout', 'Theme@saveLayout'],
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
        $content = view('theme/index', ['themes' => $themes]);

        $d = document([
            'content' => $content,
        ]);
        $d->addCss('module-theme', '/system/modules/Theme/assets/css/style.css');
        $d->title = t('module_theme');
        return $d;
    }

    public function view($name)
    {
        return $name;
    }

}
