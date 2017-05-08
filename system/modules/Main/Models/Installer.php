<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main\Models;

use Sydes\User;

class Installer
{
    public function step1()
    {
        $folders = ['', '/cache', '/iblocks', '/l10n/locales', '/l10n/translations', '/logs',
            '/modules', '/sites', '/storage', '/temp', '/thumbs'];
        foreach ($folders as $folder) {
            if (!file_exists(DIR_APP.$folder)) {
                mkdir(DIR_APP.$folder, 0777, true);
            }
        }
    }

    public function step2($locale)
    {
        app()['siteId'] = 1;
        $model = new Locales;
        $locales = ['en', $locale];
        foreach ($locales as $locale) {
            $model->downloadLocale($locale);

            if ($locale == 'en') {
                continue;
            }

            $dir = DIR_L10N.'/translations/'.$locale.'/modules';
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $model->downloadTranslations(model('Modules')->filter('default'), $locale);
        }
    }

    public function step3($params)
    {
        if ($params['timeZone'] >= 0) {
            $params['timeZone'] = '+'.$params['timeZone'];
        }

        $locales = app('api')->getLocales();
        if (!isset($locales[$params['locale']])) {
            $params['locale'] = 'en';
        }

        model('Main/User')->save(User::create([
            'username' => $params['username'],
            'password' => $params['password'],
            'email' => $params['email'],
        ]));

        model('Settings/App')->create([
            'timeZone' => 'Etc/GMT'.$params['timeZone'],
            'locale' => $params['locale'],
            'mailer_defaultFrom' => 'robot@'.$params['domain'],
            'mailer_defaultTo' => $params['email'],
        ]);

        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));

        model('Sites')->create([
            'name' => $params['siteName'],
            'theme' => $themes[0],
            'domains' => [$params['domain']],
            'onlyMainDomain' => 1,
            'locales' => [$params['locale']],
            'localeIn' => 'url',
            'work' => 1,
        ]);
    }

    public function uninstall()
    {
        removeDir(DIR_APP);
    }
}
