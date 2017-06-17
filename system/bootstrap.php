<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

define('APP_START', microtime(true));
define('SYDES_VERSION', '3.0.0-b2');

require __DIR__.'/../vendor/autoload.php';

class_alias('Sydes\Html\BS4', 'H');
class_alias('Module\Fields\Models\FormBuilder', 'Form');

mb_internal_encoding('UTF-8');

error_reporting(-1);
set_error_handler('sydesErrorHandler');

$config = require __DIR__.'/config.php';

$builder = new DI\ContainerBuilder;
$builder->addDefinitions($config);
$app = $builder->build();

$serviceLoader = $app->get('Sydes\Services\ServiceLoader');
foreach ($config['providers'] as $provider) {
    $serviceLoader->register(new $provider);
}

Sydes\App::setContainer($app);

return $app;
