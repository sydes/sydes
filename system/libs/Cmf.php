<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Cmf
{
    public function install($params)
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return null;
        }

        $folders = ['', '/cache', '/iblocks', '/l10n/locales', '/l10n/translations', '/logs',
            '/modules', '/sites', '/temp', '/thumbs'];
        foreach ($folders as $folder) {
            if (!file_exists(DIR_APP.$folder)) {
                mkdir(DIR_APP.$folder, 0777, true);
            }
        }

        if ($params['timeZone'] >= 0) {
            $params['timeZone'] = '+'.$params['timeZone'];
        }

        $locales = app('api')->getLocales();
        if (!isset($locales[$params['locale']])) {
            $params['locale'] = 'en';
        }

        $app = [
            'user' => [
                'username' => $params['username'],
                'password' => password_hash($params['password'], PASSWORD_DEFAULT),
                'mastercode' => password_hash($params['mastercode'], PASSWORD_DEFAULT),
                'email' => $params['email'],
                'autologin' => 0,
            ],
            'app' => [
                'timeZone' => 'Etc/GMT'.$params['timeZone'],
                'dateFormat' => 'd.m.Y',
                'locale' => $params['locale'],
                'emailFrom' => '',
            ],
        ];

        app()['rawAppConfig'] = $app;

        array2file($app, DIR_APP.'/config.php');

        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));

        $site = [
            'name' => $params['siteName'],
            'theme' => $themes[0],
            'domains' => [$params['domain']],
            'onlyMainDomain' => 1,
            'locales' => [$params['locale']],
            'localeIn' => 'url',
            'work' => 1,
            'modules' => [],
            'menu' => [],
        ];
        mkdir(DIR_SITE.'/1');
        app()['siteId'] = '1';
        app('site')->update($site);

        $modules = model('modules');

        $locales = ['en', $params['locale']];
        foreach ($locales as $locale) {
            $this->downloadLocale($locale);

            if ($locale == 'en') {
                continue;
            }

            $dir = DIR_L10N.'/translations/'.$locale.'/modules';
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            foreach ($modules->filter('default') as $module) {
                $this->downloadTranslation($module, $locale);
            }
        }

        $modules->install($modules->filter('default'));
    }

    public function update()
    {
        $error = false;
        /*
         * скачать архив и его хеши
         * распаковать в папку temp
         * проверить соответсвие файлов хешам
         * сделать бекап system и vendor
         * удалить папку system и vendor
         * перенести из временной папки новую версию
         * запустить миграцию базы данных для каждого сайта
         * */
        return $error;
    }

    public function uninstall()
    {
        removeDir(DIR_APP);
    }

    public function remove()
    {
        // wut?
    }

    /**
     * @param string $locale iso code
     */
    public function downloadLocale($locale)
    {
        $data = app('api')->loadLocale($locale);

        if ($data) {
            $file = DIR_L10N.'/locales/'.ucfirst($locale).'.php';
            file_put_contents($file, $data);
        }
    }

    /**
     * @param string $module
     * @param string $locale iso code
     */
    public function downloadTranslation($module, $locale)
    {
        $data = app('api')->loadTranslation($module, $locale);

        if ($data) {
            $file = DIR_L10N.'/translations/'.$locale.'/modules/'.$module.'.php';
            file_put_contents($file, $data);
        }
    }

    public function downloadExtension($type, $name)
    {
        // скачать с сайта
    }

    public function removeExtension($type, $name)
    {
        // удалить файлы
    }
}
