<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

require __DIR__.'/vendor/autoload.php';

$app = new App\App([
        'settings' => [
            'showErrorInfo' => 2,
            'checkUpdates' => false,
        ]
    ]);

$app->run();
