<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main;

use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/robots.txt', 'Main@robots');
        $r->get('/sitemap.xml', 'Main@sitemap');
    }

    public function installer()
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return 'Installed';
        }

        $r = app('request');
        if ($r->isPost()) {
            app('cmf')->install([
                'email' => $r->input('email'),
                'username' => $r->input('username'),
                'password' => $r->input('password'),
                'mastercode' => $r->input('mastercode'),
                'siteName' => 'Site Name',
                'locale' => $r->input('locale'),
                'domain' => $r->getUri()->getHost(),
                'timeZone' => $r->input('time_zone'),
            ]);

            app('user')->login($r->input('username'), $r->input('password'));

            return redirect('/admin/sites/1/edit');
        }

        $installed = glob(DIR_L10N.'/locales/*.php');
        $locales = [];

        if (empty($installed)) {
            $data = app('api')->getTranslations('Main');
            if (!is_array($data)) {
                return text('Api server down. Please, download language package manually and unzip into /app/l10n');
            }

            $all = app('api')->getLocales();
            foreach ($data as $d) {
                $locales[$d] = $all[$d];
            }
        } else {
            foreach ($installed as $package) {
                $key = str_replace([DIR_L10N.'/locales/', '.php'], '', $package);

                $className = 'Locales\\'.$key;
                $class = new $className;
                $locales[$class->getisoCode()] = $class->getNativeName();
            }
        }

        return html(render(DIR_SYSTEM.'/modules/Auth/views/form.php', [
            'locales' => $locales,
            'errors' => checkServer(),
            'title' => 'Sign up for',
            'signUp' => true,
        ]));
    }

    public function siteNotFound()
    {
        return 'siteNotFound';
    }

    public function error($code)
    {
        abort($code, 'Page not found');
    }

    public function redirect($url)
    {
        return 'redirect '.$url;
    }

    public function robots()
    {
        return 'robots content';
    }

    public function sitemap()
    {
        return 'sitemap content';
    }
}
