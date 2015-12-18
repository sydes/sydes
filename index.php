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

require __DIR__.'/system/config.php';
require DIR_VENDOR.'/autoload.php';

$app = App::instance();
$app->init();

if (!file_exists(DIR_APP.'/config.php')) {
    return $app->response->redirect('install/');
}

$response = $app->run();
$response->send();
