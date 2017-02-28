<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fileman;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET', '/admin/fileman', 'Fileman@index'],
    ];

    public function __construct()
    {

    }

	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('tools', [
            'title' => 'module_fileman',
            'url' => '#',
        ], 10);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('tools', '#');
    }

    public function index()
    {
        $d = document([
            'content' => 'fileman settings',
        ]);
        return $d;
    }
}
