<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Settings\Controllers;

use Sydes\AdminMenu;
use Sydes\Contracts\Http\Request;
use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class IndexController
{
    public function install(AdminMenu $menu, Connection $db)
    {
        $menu->addItem('system/settings', [
                'title' => 'module_settings',
            ], 40)
            ->addItem('system/settings/app', [
                'title' => 'admin_settings',
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
                'timeZones' => [
                    'Etc/GMT-12' => 'UTC+12',
                    'Etc/GMT-11' => 'UTC+11',
                    'Etc/GMT-10' => 'UTC+10',
                    'Etc/GMT-9' => 'UTC+9',
                    'Etc/GMT-8' => 'UTC+8',
                    'Etc/GMT-7' => 'UTC+7',
                    'Etc/GMT-6' => 'UTC+6',
                    'Etc/GMT-5' => 'UTC+5',
                    'Etc/GMT-4' => 'UTC+4',
                    'Etc/GMT-3' => 'UTC+3',
                    'Etc/GMT-2' => 'UTC+2',
                    'Etc/GMT-1' => 'UTC+1',
                    'UTC' => 'UTC',
                    'Etc/GMT+1' => 'UTC-1',
                    'Etc/GMT+2' => 'UTC-2',
                    'Etc/GMT+3' => 'UTC-3',
                    'Etc/GMT+4' => 'UTC-4',
                    'Etc/GMT+5' => 'UTC-5',
                    'Etc/GMT+6' => 'UTC-6',
                    'Etc/GMT+7' => 'UTC-7',
                    'Etc/GMT+8' => 'UTC-8',
                    'Etc/GMT+9' => 'UTC-9',
                    'Etc/GMT+10' => 'UTC-10',
                    'Etc/GMT+11' => 'UTC-11',
                    'Etc/GMT+12' => 'UTC-12',
                ],
                'translations' => model('Main/Translations')->getAvailable('Main'),
            ]),
        ]);

        return $d;
    }

    public function updateApp(Request $r)
    {
        $settings = model('Settings/App');
        $translations = model('Main/Translations');
        $newLang = $r->input('adminLanguage');

        // TODO test it
        if ($newLang != $settings->get('adminLanguage') && !$translations->installed('Main', $newLang)) {
            $translations->download(array_keys(app('modules')->get()), $newLang);
        }

        $settings->save(
            $r->only('timeZone', 'dateFormat', 'timeFormat', 'adminLanguage')
        );

        notify(t('saved'));

        return back();
    }
}
