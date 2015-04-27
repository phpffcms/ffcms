<?php

return [
    'basePath' => '/',
    'theme' => [
        'Front' => 'default',
        'Admin' => 'default',
        'Api' => 'default'
    ],
    'password_salt' => '$2a$10$1o81mUjA4NhQp8nSeaFmN8',
    'singleLanguage' => 'en',
    'multiLanguage' => true,
    'baseLanguage' => 'en', // do not rewrite it!
    'languages' => ['en', 'ru'],
    'siteIndex' => 'main::index',
    'database' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'ffcms',
        'username'  => 'mysql',
        'password'  => 'mysql',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => 'ffcms_'
    ]
];