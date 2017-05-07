<?php
return [
    'namespaces' => [
        'Sydes'
    ],

    'providers' => [
        'Sydes\DefaultServicesProvider',
        'Sydes\ExceptionHandlersProvider',
    ],

    'aliases' => [
        'adminMenu' => 'Sydes\AdminMenu',
        'api' => 'Sydes\Api',
        'auth' => 'Sydes\Auth',
        'cache' => 'Sydes\Cache',
        'csrf' => 'Sydes\Csrf',
        'db' => 'Sydes\Database',
        'emitter' => 'Zend\Diactoros\Response\SapiEmitter',
        'event' => 'Sydes\Event',
        'mailer' => 'Sydes\Email\Sender',
        'request' => 'Sydes\Http\Request',
        'router' => 'Sydes\Router',
        'translator' => 'Sydes\L10n\Translator',
    ],
];
