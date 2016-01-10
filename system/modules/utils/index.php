<?php

class UtilsController
{
    public function signUpForm()
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return;
        }

        $packages = glob(DIR_LANGUAGE.'/*');
        $langs = [];

        if (empty($packages)) {
            $langs = json_decode(getContentByUrl(API_HOST.'languages/list'), true);
            if (!is_array($langs)) {
                return 'Please, download language package manually and unzip into /app/languages';
            }
        } else {
            foreach ($packages as $package) {
                $key = str_replace(DIR_LANGUAGE.'/', '', $package);
                $arr = include $package.'/translation.php';
                $langs[$key] = $arr['lang_native_name'];
            }
        }

        return render(DIR_SYSTEM.'/modules/user/views/login-signup.php', [
            'langs' => $langs,
            'lang' => app('request')->getPreferredLanguage(array_keys($langs)),
            'errors' => checkServer(),
            'title' => 'Sign up for',
            'signUp' => true,
        ]);
    }

    public function signUp()
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return;
        }

        $post = app('request')->request;
        if ($post['time_zone'] >= 0) {
            $post['time_zone'] = '+'.$post['time_zone'];
        }

        $config = [
            'user' => [
                'username' => $post['username'],
                'pass' => md5($post['password']),
                'mastercode' => md5($post['mastercode']),
                'email' => $post['email'],
                'autologin' => 0,
            ],
            'app' => [
                'time_zone' => 'Etc/GMT'.$post['time_zone'],
                'date_format' => 'd.m.Y',
                'check_updates' => 1,
                'language' => $post['language'],
                'skin' => 'black',
                'debug' => 0,
            ],
        ];

        arr2file($config, DIR_APP.'/config.php');

        $langs = ['en', $post['language']];
        foreach ($langs as $lang) {
            $langDir = DIR_LANGUAGE.'/'.$lang;
            if (!file_exists($langDir)) {
                mkdir($langDir, 0777, true);
                extractOuterZip($langDir, API_HOST.'languages/download/core/'.$lang);
            }
        }

        (new App\User($config['user']))->login($post['username'], $post['password']);
        $_SESSION['admin'] = 1;

        // install first site
        app('translator')->loadPackage($post['language']);
        $config = [
            'name' => t('first_site'),
            'theme' => 'default',
            'domains' => [app('request')->domain],
            'locales' => [substr($post['language'], 0, 2)],
            'work' => 1,
            'need_cache' => 0,
            'use_alias_as_path' => 0,
            'page_types' => [
                'page'  => [
                    'title' => t('pages'),
                    'layout' => 'page',
                    'structure' => 'tree',
                    'root' => 0,
                    'form' => [],
                ],
                'trash' => [
                    'title' => t('trash'),
                    'layout' => 'page',
                    'structure' => 'list',
                    'root' => 0,
                    'form' => [],
                    'list' => [],
                ],
            ],
            'modules' => [],
        ];
        mkdir(DIR_SITE.'/s1', 777);
        arr2file($config, DIR_SITE.'/s1/config.php');

        return redirect('admin/sites/1/edit');
    }

}
