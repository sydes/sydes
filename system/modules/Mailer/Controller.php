<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Mailer;

use Sydes\AdminMenu;

class Controller
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('modules/services/mailer', [
            'title' => 'module_mailer',
            'url' => '/admin/mailer',
        ], 10);
    }

    public function index()
    {
        $d = document([
            'title' => t('module_mailer'),
            'content' => 'mailer',
        ]);

        return $d;
    }
}
