<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Settings;

use Sydes\AdminMenu;
use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class Controller
{
    public function install(AdminMenu $menu, Connection $db)
    {
        $menu->addItem('system/settings', [
                'title' => 'module_settings',
            ], 40)
            ->addItem('system/settings/app', [
                'title' => 'app_settings',
                'url' => '/admin/app',
            ], 0);

        $db->getSchemaBuilder()->create('settings', function (Blueprint $t) {
            $t->increments('id');
            $t->string('extension');
            $t->string('key');
            $t->string('value');

            $t->unique(['extension', 'key']);
        });
    }

    public function editApp()
    {
        $d = document([
            'title' => t('module_settings'),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view('settings/form', [
                'data' => model('Settings/App')->get(),
                'options' => [
                    'method' => 'put',
                    'url' => '/admin/app',
                    'form' => 'main',
                ],
            ]),
        ]);

        $d->addJs('settings.js', assetsPath('Settings').'/js/settings.js');

        return $d;
    }

    public function updateApp()
    {
        $data = app('request')->only('timeZone', 'dateFormat', 'timeFormat', 'locale');
        model('Settings/App')->save($data);

        notify(t('saved'));

        return back();
    }
}
