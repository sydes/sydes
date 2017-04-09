<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes;

class LayoutsController
{
    public function view($name)
    {
        return $name;
    }

    public function save($name)
    {
        notify(t('saved').' '.$name);

        return back();
    }
}
