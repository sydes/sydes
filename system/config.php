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
        'csrf' => 'Sydes\Csrf',
        'emitter' => 'Zend\Diactoros\Response\SapiEmitter',
        'event' => 'Sydes\Event',
        'router' => 'Sydes\Router',
        'translator' => 'Sydes\L10n\Translator',
    ],
];
