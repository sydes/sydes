<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

$app = require __DIR__.'/system/bootstrap.php';

$runner = $app->make('System\Runner');

$runner->run();
