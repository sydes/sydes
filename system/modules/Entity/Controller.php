<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity;

class Controller
{
    public function index()
    {
        $d = document([
            'content' => 'All Entities',
        ]);

        return $d;
    }
}