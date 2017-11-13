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
        $folders = ['', '/cache', '/iblocks', '/languages', '/logs',
            '/modules', '/sites', '/storage', '/temp', '/thumbs'];
        $appDir = app('dir.app');

        foreach ($folders as $folder) {
            if (!file_exists($appDir.$folder)) {
                mkdir($appDir.$folder, 0777, true);
            }
        }
    }

    public function step2($locale)
    {
        app()->set('site.id', 1);

        $dir = app('dir.l10n').'/'.$locale.'/modules';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        model('Main/Translations')->download(model('Modules')->filter('default'), $locale);
    }

    public function step3($params)
    {
        app()->set('site.id', 1);

        if ($params['timeZone'] >= 0) {
            $params['timeZone'] = '+'.$params['timeZone'];
        }

        if (!class_exists('Sydes\L10n\Locales\\'.ucfirst($params['locale']).'Locale')) {
            $params['locale'] = 'en';
        }

        $user = User::create([
            'id' => 1,
            'username' => $params['username'],
            'password' => $params['password'],
            'email' => $params['email'],
        ]);
        model('Main/User')->save($user);
        app('auth')->login($user, true);

        model('Settings/App')->create([
            'timeZone' => 'Etc/GMT'.$params['timeZone'],
            'adminLanguage' => $params['locale'],
        ]);

        $themes = str_replace(app('dir.theme').'/', '', glob(app('dir.theme').'/*', GLOB_ONLYDIR));

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
        removeDir(app('dir.app'));
    }
}
