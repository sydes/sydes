<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Util;

class Controller
{
    public function install()
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return 'Installed';
        }

        $r = app('request');
        if ($r->isPost()) {
            \App\Cmf::install([
                'email' => $r->input('email'),
                'username' => $r->input('username'),
                'password' => $r->input('password'),
                'mastercode' => $r->input('mastercode'),
                'siteName' => 'Site Name',
                'locale' => $r->input('locale'),
                'domain' => $r->getUri()->getHost(),
                'timeZone' => $r->input('time_zone'),
            ]);
            return redirect('/admin/sites/1/edit');
        }

        $packages = glob(DIR_LANGUAGE.'/*');
        $langs = [];

        if (empty($packages)) {
            $data = json_decode(getContentByUrl(API_HOST.'translations/core'), true);
            if (!is_array($data)) {
                return 'Please, download language package manually and unzip into /app/languages';
            }
            foreach ($data as $d) {
                $langs[$d['language']] = $d['native_name'];
            }
        } else {
            foreach ($packages as $package) {
                $key = str_replace(DIR_LANGUAGE.'/', '', $package);
                $arr = include $package.'/translation.php';
                $langs[$key] = $arr['lang_native_name'];
            }
        }

        return html(render(DIR_SYSTEM.'/modules/User/views/login-signup.php', [
            'locales' => $langs,
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
        return 'error '.$code;
    }

    public function redirect($url)
    {
        return 'redirect '.$url;
    }

    public function robots()
    {
        return 'robots content';
    }
}
