<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

require __DIR__.'/vendor/autoload.php';

$app = new Sydes\App([
        'settings' => [
            'debugLevel' => 2,
            'checkUpdates' => false,
        ]
    ]);

$app->run();
