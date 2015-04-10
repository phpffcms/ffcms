<?php

return [
    'basePath' => '/',
    'theme' => 'default',
    'password_salt' => '$2a$10$1o81mUjA4NhQp8nSeaFmN8',
    'singleLanguage' => 'en',
    'multiLanguage' => true,
    'baseLanguage' => 'en', // do not rewrite it!
    'languages' => ['en', 'ru'],
    'siteIndex' => 'main::index',
    'database' => [
        'main' => 'mysql://mysql:mysql@127.0.0.1/ffcms' // mysql://username:password@localhost/development
    ]
];