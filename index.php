<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

define('VERSION', '3.0.0-a');
define('SYDES_START', microtime(true));

require __DIR__.'/vendor/autoload.php';
$app = require DIR_SYSTEM.'/bootstrap.php';

if (!file_exists(DIR_APP.'/config.php')) {
    $app['response']->redirect('install/');
}

$app->init();

$response = $app->run();
$response->send();
