<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Forms;

use App\Cmf;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/forms', 'Forms@index');
    }

    public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('constructors', [
            'title' => 'module_forms',
            'url' => '/admin/forms',
        ], 10);
    }

    public function uninstall(Cmf  $cmf)
    {
        $cmf->removeMenuItem('constructors', '/admin/forms');
    }

    public function index()
    {
        $d = document([
            'content' => 'forms index',
        ]);
        return $d;
    }
}
