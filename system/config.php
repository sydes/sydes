<?php
use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;
use Interop\Container\ContainerInterface;

return [
    'providers' => [
        'Sydes\Services\DefaultServicesProvider',
        'Sydes\Exception\ExceptionHandlersProvider',
        'Sydes\View\ViewServiceProvider',
    ],

    'Sydes\AdminMenu' => DI\object()->constructor(DI\string('{dir.site}/{site.id}/menu.php')),
    'Sydes\Api' => DI\object()->constructor(SYDES_VERSION, 'http://api.sydes.ru/'),
    'Sydes\Auth' => DI\object()->constructor(DI\get('Module\Main\Models\User')),
    'Sydes\Cache' => DI\object()->constructor(\DI\get('dir.cache')),
    'Sydes\Database' => DI\object()->constructor(\DI\get('db.config')),
    'Sydes\L10n\Translator' => DI\object()->constructor(DI\string('{dir.l10n}/translations')),

    'renderer' => function (ContainerInterface $c) {
        $class = 'System\Renderer\\'.ucfirst($c->get('section'));
        return $c->make($class);
    },
    'app' => function (ContainerInterface $c) {
        $path = $c->get('dir.storage').'/app.php';
        return new Settings($path, new FileDriver());
    },
    'site' => function (ContainerInterface $c) {
        $path = $c->get('dir.site.this').'/config.php';
        return new Settings($path, new FileDriver());
    },

    // aliases
    'admin.menu' => DI\get('Sydes\AdminMenu'),
    'api'        => DI\get('Sydes\Api'),
    'auth'       => DI\get('Sydes\Auth'),
    'cache'      => DI\get('Sydes\Cache'),
    'csrf'       => DI\get('Sydes\Csrf'),
    'db'         => DI\get('Sydes\Database'),
    'emitter'    => DI\get('Zend\Diactoros\Response\SapiEmitter'),
    'event'      => DI\get('Sydes\Event'),
    'mailer'     => DI\get('Sydes\Email\Sender'),
    'request'    => DI\get('Sydes\Http\Request'),
    'router'     => DI\get('Sydes\Router\Router'),
    'translator' => DI\get('Sydes\L10n\Translator'),

    // parameters
    'db.config' => [
        'default' => 'site',
        'connections' => [
            'site' => [
                'driver' => 'sqlite',
                'database' => '{dir.site.this}/database.db',
            ],
        ],
    ],

    // directories
    'dir.root'    => realpath(__DIR__.'/../'),
    'dir.app'     => DI\string('{dir.root}/app'),
    'dir.system'  => DI\string('{dir.root}/system'),
    'dir.theme'   => DI\string('{dir.root}/themes'),
    'dir.vendor'  => DI\string('{dir.root}/vendor'),
    'dir.cache'   => DI\string('{dir.app}/cache'),
    'dir.cache.route' => DI\string('{dir.cache}/routes.{site.id}.cache'),
    'dir.iblock'  => DI\string('{dir.app}/iblocks'),
    'dir.l10n'    => DI\string('{dir.app}/l10n'),
    'dir.logs'    => DI\string('{dir.app}/logs'),
    'dir.module'  => DI\string('{dir.app}/modules'),
    'dir.site'    => DI\string('{dir.app}/sites'),
    'dir.site.this' => DI\string('{dir.site}/{site.id}'),
    'dir.storage' => DI\string('{dir.app}/storage'),
    'dir.temp'    => DI\string('{dir.app}/temp'),

    'section' => 'base',
    'settings' => [
        'cacheRouter'  => true,
        'debugLevel'   => 2,
        'checkUpdates' => true,
    ],
];
