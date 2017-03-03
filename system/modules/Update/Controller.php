<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Update;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/update', 'Update@index'],
    ];

	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('tools', [
            'title' => 'module_update',
            'url' => '/admin/update',
        ], 240);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('tools', '/admin/themes');
    }

    public function index()
    {
        $d = document([
            'content' => 'Current version: '.SYDES_VERSION,
        ]);
        $d->title = t('module_update');
        return $d;
    }
}
