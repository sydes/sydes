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
    public static function install($params)
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return null;
        }

        $folders = ['', '/cache', '/iblocks', '/languages', '/logs', '/modules', '/sites', '/temp', '/thumbs'];
        foreach ($folders as $folder) {
            if (!file_exists(DIR_APP.$folder)) {
                mkdir(DIR_APP.$folder, 0777, true);
            }
        }

        if ($params['timeZone'] >= 0) {
            $params['timeZone'] = '+'.$params['timeZone'];
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

        array2file($app, DIR_APP.'/config.php');

        $langs = ['en', $params['locale']];
        foreach ($langs as $lang) {
            $langDir = DIR_LANGUAGE.'/'.$lang;
            if (!file_exists($langDir)) {
                mkdir($langDir, 0777, true);
                extractOuterZip($langDir, API_HOST.'translations/download/core/'.$lang);
            }
        }

        $themes = str_replace(DIR_THEME.'/', '', glob(DIR_THEME.'/*', GLOB_ONLYDIR));

        $site = [
            'name' => $params['siteName'],
            'theme' => $themes[0],
            'domains' => [$params['domain']],
            'locales' => [substr($params['locale'], 0, 2)],
            'work' => 1,
            'need_cache' => 0,
            'modules' => [],
            'routes' => [],
            'menu' => [
                'content' => ['weight' => 0,   'title' => 'menu_content', 'icon' => 'file', 'items' => []],
                'modules' => ['weight' => 100, 'title' => 'menu_modules', 'icon' => 'th-list', 'items' => []],
                'constructors' => ['weight' => 200, 'title' => 'menu_constructors',   'icon' => 'th', 'items' => [
                    ['weight' => 100, 'title' => 'module_form', 'url' => '/admin/form'],
                    ['weight' => 200, 'title' => 'module_slider', 'url' => '/admin/slider'],
                ]],
                'tools' => ['weight' => 300, 'title' => 'menu_tools',   'icon' => 'wrench', 'items' => [
                    ['weight' => 100, 'title' => 'module_fileman', 'url' => '#'],
                    ['weight' => 200, 'title' => 'module_import', 'url' => '/admin/import'],
                ]],
                'system' => ['weight' => 400, 'title' => 'menu_system',  'icon' => 'cog', 'items' => [
                    ['weight' => 100, 'title' => 'module_settings', 'url' => '/admin/settings'],
                    ['weight' => 400, 'title' => 'module_logs', 'url' => '/admin/logs'],
                ]],
            ],
        ];
        mkdir(DIR_SITE.'/s1');
        app()['site'] = ['id' => 's1'];

        self::saveSiteConfig($site);
        self::installDefaultModules();
    }

    public static function installDefaultModules(){
        $modules = [
            'Page',
            'Route',
            'Theme',
            'Iblock',
        ];

        foreach ($modules as $module) {
            App::execute([$module.'@install']);
        }
    }

    public static function update()
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

    public static function uninstall()
    {
        removeDir(DIR_APP);
    }

    public static function remove()
    {
        // wut?
    }

    public static function downloadExtension($type, $name)
    {
        // скачать с сайта
    }

    public static function removeExtension($type, $name)
    {
        // удалить файлы
    }

    /**
     * @param string $name
     * @param array  $data ['handlers' => [], 'iblocks' => []]
     */
    public static function installModule($name, array $data = [])
    {
        $config = app('rawSiteConfig');
        if (!isset($config['modules'][$name])) {
            $dir = moduleDir($name).'/iblocks/';
            $iblocks = str_replace($dir, '', glob($dir.'*'));
            $data['iblocks'] = $iblocks;

            $config['modules'][$name] = $data;

            self::saveSiteConfig($config);
        }
    }

    /**
     * @param string $name
     */
    public static function uninstallModule($name)
    {
        $config = app('rawSiteConfig');
        if (isset($config['modules'][$name])) {
            unset($config['modules'][$name]);

            self::saveSiteConfig($config);
        }
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $icon
     * @param int    $weight
     */
    public static function addMenuGroup($name, $title, $icon = 'asterisk', $weight = 150)
    {
        $config = app('rawSiteConfig');
        if (!isset($config['menu'][$name])) {
            $config['menu'][$name] = [
                'weight' => $weight,
                'title' => $title,
                'icon' => $icon,
                'items' => []
            ];

            self::saveSiteConfig($config);
        }
    }

    /**
     * @param string $name
     */
    public static function removeMenuGroup($name)
    {
        $config = app('rawSiteConfig');
        if (isset($config['menu'][$name])) {
            unset($config['menu'][$name]);

            self::saveSiteConfig($config);
        }
    }

    /**
     * @param string $name
     * @param array $data ['title' => '', 'url' => '', 'quick_add' => true]
     * @param int $weight
     */
    public static function addMenuItem($name, $data, $weight = 150)
    {
        $config = app('rawSiteConfig');
        if (isset($config['menu'][$name])) {
            $config['menu'][$name]['items'][] = array_merge(['weight' => $weight], $data);

            self::saveSiteConfig($config);
        }
    }

    /**
     * @param string $group
     * @param string $url
     */
    public static function removeMenuItem($group, $url)
    {
        $config = app('rawSiteConfig');
        if (isset($config['menu'][$group])) {
            foreach ($config['menu'][$group]['items'] as $i => $item) {
                if ($item['url'] == $url) {
                    unset($config['menu'][$group]['items'][$i]);

                    self::saveSiteConfig($config);

                    break;
                }
            }
        }
    }

    /**
     * @param string $name name of extension
     * @param array  $data array of arrays ['method', 'uri', 'route']
     */
    public static function addRoutes($name, array $data)
    {
        $config = app('rawSiteConfig');
        if (isset($config['routes'][$name])) {
            $config['routes'][$name] = array_merge($config['routes'][$name], $data);
        } else {
            $config['routes'][$name] = $data;
        }

        self::saveSiteConfig($config);
    }

    /**
     * @param string $name name of extension
     */
    public static function removeRoutes($name)
    {
        $config = app('rawSiteConfig');
        if (isset($config['routes'][$name])) {
            unset($config['routes'][$name]);

            self::saveSiteConfig($config);
        }
    }

    private static function saveSiteConfig($config)
    {
        app()['rawSiteConfig'] = $config;
        array2file($config, DIR_SITE.'/'.app('site')['id'].'/config.php');
    }
}
