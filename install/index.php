<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

require __DIR__.'/../vendor/autoload.php';
$app = require DIR_SYSTEM.'/bootstrap.php';

if (file_exists(DIR_APP.'/config.php')) {
    die('Delete this folder, pls');
}

if ($app['request']->is_post && $app['request']->has('username')) {
    $folders = ['cache', 'iblocks', 'languages', 'logs', 'modules', 'plugins', 'sites', 'temp', 'thumbs'];
    foreach ($folders as $folder) {
        if (!file_exists(DIR_APP.'/'.$folder)) {
            mkdir(DIR_APP.'/'.$folder, 0777, true);
        }
    }

    $post = $app['request']->request;
    if ($post['time_zone'] >= 0) {
        $post['time_zone'] = '+'.$post['time_zone'];
    }

    $config = [
        'user' => [
            'username' => $post['username'],
            'password' => md5($post['password']),
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

    $lang_dir = DIR_LANGUAGE.'/'.$post['language'];
    if (!file_exists($lang_dir)) {
        mkdir($lang_dir, 0777, true);
        extractOuterZip($lang_dir, 'http://translate.sydes.ru/download?ext=core&lang='.$post['language']);
    }

    echo 'installed';

    // залогиниться и ввести мастеркод
    // $app['response']->alert('Delete install folder!');
    // $app['response']->redirect('admin');
} else {
    $packages = glob(DIR_LANGUAGE.'/*');
    $langs = [];

    if (empty($packages)) {
        $langs = json_decode(getContentByUrl('http://translate.sydes.ru/list'), true);
    } else {
        foreach ($packages as $package) {
            $key = str_replace(DIR_LANGUAGE.'/', '', $package);
            $arr = include $package.'/translation.php';
            $langs[$key] = $arr['lang_native_name'];
        }
    }

    echo render('form.php', [
        'langs' => $langs,
        'errors' => checkServer()
    ]);
}
