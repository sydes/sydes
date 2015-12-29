<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

session_start();

$app = App\App::getInstance();

$app['exception_handler'] = function () {
    return new App\Exception\ExceptionHandler;
};

$handler = new App\Exception\Handler;

$app['request'] = function () {
    return App\Http\Request::capture();
};

return $app;