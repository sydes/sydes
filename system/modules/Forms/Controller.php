<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Forms;

class Controller
{
    public static $routes = [
        ['GET', '/admin/forms', 'Forms@index'],
    ];

    public function __construct()
    {

    }

	public function install($cmf)
    {
        $cmf->installModule('forms');

        $cmf->addMenuItem('constructors', [
            'title' => 'module_forms',
            'url' => '/admin/forms',
        ], 10);
    }

	public function uninstall($cmf)
    {
        $cmf->removeMenuItem('constructors', '/admin/forms');
        $cmf->uninstallModule('forms');
    }

    public function index()
    {
        $d = document([
            'content' => 'forms index',
        ]);
        return $d;
    }
}
