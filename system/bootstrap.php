<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

session_start();
mb_internal_encoding('UTF-8');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = token(16);
}

$app = App\App::getInstance();
$handler = new App\Exception\Handler;

$app['exception_handler'] = function () {
    return new App\Exception\ExceptionHandler;
};
$app['request'] = function () {
    return App\Http\Request::capture();
};
$app['cache'] = function () {
    return new App\Cache(DIR_CACHE);
};
$app['translator'] = function () {
    return new App\Translator;
};
$app['event'] = function () {
    return new App\Event;
};

$app['translator']->loadPackage();
$app['preferredLanguage'] = $app['request']->getPreferredLanguage($app['translator']->installedPackages);

$plugins = glob(DIR_PLUGIN.'/*/index.php');
$plugins[] = DIR_SYSTEM.'/plugins.php';
foreach ($plugins as $plugin) {
    include $plugin;
}
$app['event']->trigger('after.bootstrap');

return $app;
