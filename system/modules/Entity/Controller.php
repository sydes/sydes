<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity;

use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/entity', 'Entity@index');
    }

    public function index()
    {
        $d = document([
            'content' => 'All Entities',
        ]);

        return $d;
    }
}
