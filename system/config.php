<?php
return [
    'providers' => [
        'Sydes\Services\DefaultServicesProvider',
        'Sydes\Exception\ExceptionHandlersProvider',
        'Sydes\View\ViewServiceProvider',
    ],

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
                'database' => '{dir.site}/{site.id}/database.db',
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
    'dir.iblock'  => DI\string('{dir.app}/iblocks'),
    'dir.l10n'    => DI\string('{dir.app}/l10n'),
    'dir.logs'    => DI\string('{dir.app}/logs'),
    'dir.module'  => DI\string('{dir.app}/modules'),
    'dir.site'    => DI\string('{dir.app}/sites'),
    'dir.storage' => DI\string('{dir.app}/storage'),
    'dir.temp'    => DI\string('{dir.app}/temp'),

    'section' => 'base',
    'settings' => [
        'cacheRouter'  => true,
        'debugLevel'   => 2,
        'checkUpdates' => true,
    ],
];
