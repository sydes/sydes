<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Settings;

use Sydes\AdminMenu;
use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/app', 'Settings@editApp');
        $r->put('/admin/app', 'Settings@updateApp');
    }

    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/settings', [
                'title' => 'module_settings',
                'url' => '#',
            ], 40)
            ->addItem('system/settings/app', [
                'title' => 'app_settings',
                'url' => '/admin/app',
            ], 0);
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
        $data = app('request')->only('timeZone', 'dateFormat', 'timeFormat', 'locale', 'mailer_defaultFrom',
            'mailer_defaultTo', 'mailer_useSmtp', 'mailer_smtpHost', 'mailer_smtpPort',
            'mailer_smtpUser', 'mailer_smtpPassword', 'mailer_sendAlso');
        model('Settings/App')->save($data);

        notify(t('saved'));

        return back();
    }
}
