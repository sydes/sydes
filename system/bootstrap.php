<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

$app = App::getInstance();

$app['request'] = function () {
    return new HttpRequest;
};

$app['response'] = function () {
    return new Response;
};

return $app;