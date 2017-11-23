<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Route\Controllers;

class IndexController
{
    public function install(Models\Route $model)
    {
        $model->make();
    }

    public function uninstall(Models\Route $model)
    {
        $model->drop();
    }
}
