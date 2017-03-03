<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Pages;

use App\Cmf;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/pages', 'Pages@index');
    }

    public function install(Cmf $cmf)
    {
        // add menu group
    }

    public function uninstall(Cmf $cmf)
    {
        // remove menu group
    }

    public function index()
    {
        return 'page index or tree';
    }

    public function view($path)
    {
        return 'qqq'.$path;
    }
}
