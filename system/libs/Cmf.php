<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
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
                'adminSkin' => 'black',
                'emailFrom' => '',
            ],
        ];

        app()['rawAppConfig'] = $app;

        array2file($app, DIR_APP.'/config.php');

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

            foreach ($this->getDefaultModules() as $module) {
                $this->downloadTranslation($module, $locale);
            }
        }

        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));

        $site = [
            'name' => $params['siteName'],
            'theme' => $themes[0],
            'domains' => [$params['domain']],
            'locales' => [$params['locale']],
            'localeIn' => 'url',
            'work' => 1,
            'modules' => [],
            'menu' => [
                'content' => ['weight' => 0,   'title' => 'menu_content', 'icon' => 'file', 'items' => []],
                'modules' => ['weight' => 100, 'title' => 'menu_modules', 'icon' => 'th-list', 'items' => []],
                'constructors' => ['weight' => 200, 'title' => 'menu_constructors', 'icon' => 'th', 'items' => []],
                'tools' => ['weight' => 300, 'title' => 'menu_tools',   'icon' => 'wrench', 'items' => []],
                'system' => ['weight' => 400, 'title' => 'menu_system',  'icon' => 'cog', 'items' => []],
            ],
        ];
        mkdir(DIR_SITE.'/1');
        app()['siteId'] = '1';
        app('site')->update($site);

        $this->installDefaultModules();
    }

    private function getDefaultModules()
    {
        return str_replace(DIR_SYSTEM.'/modules/', '', glob(DIR_SYSTEM.'/modules/*', GLOB_ONLYDIR));
    }

    private function installDefaultModules()
    {
        $modules = $this->getDefaultModules();
        foreach ($modules as $name) {
            $this->installModule($name);
        }
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

    /**
     * @param string $name
     */
    public function installModule($name)
    {
        $modules = app('site')->get('modules');
        $name = studly_case($name);
        if (isset($modules[$name])) {
            return;
        }

        $data = [];
        $dir = moduleDir($name);

        $iblocks = str_replace($dir.'/iblocks/', '', glob($dir.'/iblocks/*'));
        if (!empty($iblocks)) {
            $data['iblocks'] = $iblocks;
        }

        $files = str_replace($dir.'/functions/', '', glob($dir.'/functions/*'));
        if (!empty($files)) {
            $data['files'] = $files;
        }

        if (file_exists($dir.'/Handlers.php')) {
            $data['handlers'] = true;
        }

        if (file_exists($dir.'/Cli.php')) {
            $data['console'] = true;
        }

        $modules[$name] = $data;

        app('site')->update('modules', $modules);

        App::execute([$name.'@install', [$this]], true);

        app('cache')->flush();
    }

    /**
     * @param string $name
     */
    public function uninstallModule($name)
    {
        $modules = app('site')->get('modules');
        $name = studly_case($name);
        if (isset($modules[$name])) {
            unset($modules[$name]);

            app('site')->update('modules', $modules);

            App::execute([$name.'@uninstall', [$this]], true);

            app('cache')->flush();
        }
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $icon
     * @param int    $weight
     */
    public function addMenuGroup($name, $title, $icon = 'asterisk', $weight = 150)
    {
        $menu = app('site')->get('menu');
        if (isset($menu[$name])) {
            return;
        }

        $menu[$name] = [
            'weight' => $weight,
            'title' => $title,
            'icon' => $icon,
            'items' => []
        ];

        app('site')->update('menu', $menu);
    }

    /**
     * @param string $name
     */
    public function removeMenuGroup($name)
    {
        $menu = app('site')->get('menu');
        if (isset($menu[$name])) {
            unset($menu[$name]);

            app('site')->update('menu', $menu);
        }
    }

    /**
     * @param string $name
     * @param array $data ['title' => '', 'url' => '', 'quick_add' => true]
     * @param int $weight
     */
    public function addMenuItem($name, $data, $weight = 150)
    {
        $menu = app('site')->get('menu');
        if (isset($menu[$name])) {
            $menu[$name]['items'][] = array_merge(['weight' => $weight], $data);

            app('site')->update('menu', $menu);
        }
    }

    /**
     * @param string $group
     * @param string $url
     */
    public function removeMenuItem($group, $url)
    {
        $menu = app('site')->get('menu');
        if (!isset($menu[$group])) {
            return;
        }

        foreach ($menu[$group]['items'] as $i => $item) {
            if ($item['url'] == $url) {
                unset($menu[$group]['items'][$i]);

                app('site')->update('menu', $menu);

                break;
            }
        }
    }
}
