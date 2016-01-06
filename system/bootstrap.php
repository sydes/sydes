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
$app['load'] = function () {
    return new App\Loader;
};

$app['translator'] = function () {
    return new App\Translator;
};
$app['translator']->loadPackage();
$app['preferredLanguage'] = $app['request']->getPreferredLanguage($app['translator']->installedPackages);

$app['event'] = function () {
    return new App\Event;
};
$plugins = glob(DIR_PLUGIN.'/*/index.php');
$plugins[] = DIR_SYSTEM.'/plugins.php';
foreach ($plugins as $plugin) {
    include $plugin;
}
$app['event']->trigger('after.bootstrap');

return $app;
