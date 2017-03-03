<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Import;

use App\Cmf;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/import', 'Import@index');
    }

	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('tools', [
            'title' => 'module_import',
            'url' => '/admin/import',
        ], 300);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('tools', '/admin/import');
    }

    public function index()
    {
        $d = document([
            'content' => 'import/export forms',
        ]);
        return $d;
    }
}
