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
        'emailSender' => 'Sydes\Email\Sender',
        'emitter' => 'Zend\Diactoros\Response\SapiEmitter',
        'event' => 'Sydes\Event',
        'request' => 'Sydes\Http\Request',
        'router' => 'Sydes\Router',
        'translator' => 'Sydes\L10n\Translator',
    ],
];
