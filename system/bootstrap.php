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
    return new App\Loader();
};

$app['renderer'] = function ($c) {
    return (strpos($c['request']->url, ADMIN.'/') === 1) ? new App\Renderer\Admin() : new App\Renderer\Front();
};




return $app;