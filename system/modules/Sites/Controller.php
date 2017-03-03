<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Sites;

use App\Cmf;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/sites', 'Sites@index');
        $r->get('/admin/sites/{id:\d+}', 'Sites@edit');
        $r->post('/admin/sites/{id:\d+}', 'Sites@update');
    }

    public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('system', [
            'title' => 'module_sites',
            'url' => '/admin/sites',
        ], 10);
    }

    public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('system', '/admin/sites');
    }

    public function index()
    {
        $d = document([
            'content' => 'list of sites',
        ]);
        return $d;
    }

    public function edit($id)
    {
        $d = document([
            'content' => 'site s'.$id.' editor',
        ]);
        return $d;
    }
}
