<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Forms;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/forms', 'Forms@index'],
    ];

    public function __construct()
    {

    }

	public function install()
    {
        Cmf::installModule('forms');

        Cmf::addMenuItem('constructors', [
            'title' => 'module_forms',
            'url' => '/admin/forms',
        ], 10);
    }

	public function uninstall()
    {
        Cmf::removeMenuItem('constructors', '/admin/forms');
        Cmf::uninstallModule('forms');
    }

    public function index()
    {
        $d = document([
            'content' => 'forms index',
        ]);
        return $d;
    }
}
