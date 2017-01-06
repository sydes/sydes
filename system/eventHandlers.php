<?php
defined('APP_START') or die;

$events = app('event');

/**
 * Csrf guard
 */
$events->on('route.found', '*', [app('csrf'), 'check']);

/**
 * Auth middleware :)
 */
$events->on('route.found', 'admin/*', function () {
    if (!app('user')->isEditor()) {
        $_SESSION['entry'] = app('request')->getUri()->getPath();
        throw new App\Exception\RedirectException('/login');
    }
});

/**
 * Check updates for cmf in admin center
 */
$events->on('module.executed', 'admin/*', function () {
    if (!app('settings')['checkUpdates']) {
        return;
    }

    $cache = app('cache');
    $cache->remember('update_checked', function () use ($cache) {
        $need = getContentByUrl(API_HOST.'update?version='.VERSION.'&site='.md5($_SERVER['HTTP_HOST']));
        $updateText = 0;
        if ($need == 1) {
            $updateText = t('common_update_cms');
        } elseif ($need == 2) {
            $updateText = t('security_update_cms');
        }
        $cache->put('update_text', $updateText, 600);
        return 1;
    }, 86400);

    $update_text = $cache->get('update_text');
    if ($update_text) {
        alert($update_text);
    }
});
