<?php
$app = app();
$app['event']->on('after.system.init', '*', function() use ($app){
    if ($app['section'] == 'admin'){
        if (!App\Auth::admin()) {
            throw new App\Exception\RedirectException('/admin/login');
        }
    }
});

$app['event']->on('before.render', 'admin/*', function (App\Document &$doc) use ($app) {
    if (!$app['config']['app']['check_updates']) {
        return;
    }

    $app['cache']->remember('update_checked', function () use ($app) {
        $need = file_get_contents('http://sydes.ru/update/?version='.VERSION.'&site='.md5($_SERVER['HTTP_HOST']));
        $update_text = 0;
        if ($need == 1) {
            $update_text = t('common_update_cms');
        } elseif ($need == 2) {
            $update_text = t('security_update_cms');
        }
        $app['cache']->put('update_text', $update_text, 600);
        return 1;
    }, 86400);

    $update_text = $app['cache']->get('update_text');
    if ($update_text) {
        $doc->alert($update_text);
    }
});
