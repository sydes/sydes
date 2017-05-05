<?php
return [
    'default' => 'site',

    'connections' => [
        'site' => [
            'driver' => 'sqlite',
            'database' => DIR_SITE.'/'.app('siteId').'/database.db',
        ],
    ],
];
