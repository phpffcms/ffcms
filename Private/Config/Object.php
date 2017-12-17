<?php

use Ffcms\Core\Helper\FileSystem\File;
use \Illuminate\Database\Capsule\Manager as Capsule;
use Ffcms\Core\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

// define timezone
date_default_timezone_set(App::$Properties->get('timezone'));

return [
    'Database' => function () {
        $capsule = new Capsule;
        if (env_name !== 'Install') {
            try {
                $capsule->addConnection(App::$Properties->get('database'));
            } catch (\Exception $e) {
                exit('Database connection error!');
            }
        }

        $capsule->setAsGlobal(); // available from any places
        $capsule->bootEloquent(); // allow active record model's
        if (\App::$Debug !== null) { // enable query collector
            $capsule->connection()->enableQueryLog();
        }

        // if this is not installer interface and cms is not installed - try to redirect to install interface
        if (env_name !== 'Install' && !File::exist('/Private/Install/install.lock')) {
            try {
                $capsule->connection()->getPdo();
            } catch (\Exception $e) {
                $instUri = \App::$Alias->scriptUrl . '/install';
                \App::$Response->redirect($instUri, true);
            }
        }

        return $capsule;
    },
    'Session' => function() {
        $handler = null;
        try {
            $pdo = \App::$Database->connection()->getPdo();
            $handler = new PdoSessionHandler($pdo, [
                'db_table' => App::$Properties->get('database')['prefix'] . 'sessions'
            ]);
        } catch (Exception $e) {
            $handler = new NativeFileSessionHandler(root . '/Private/Sessions');
        }

        $storage = new NativeSessionStorage([
            'cookie_lifetime' => 86400, // 86400 = 24 * 60 * 60 = 24 hours
            'gc_maxlifetime' => 86400,
            'cookie_httponly' => '1'
        ], $handler);

        return new Session($storage);
    },
    'User' => function () {
        return new Apps\ActiveRecord\User();
    },
    'Mailer' => function () {
        $swiftTransport = new Swift_SendmailTransport();
        return (new Swift_Mailer($swiftTransport));
    },
    'Captcha' => function () {
        return new Extend\Core\Captcha\Gregwar();
    },
    'Cache' => function () {
        // initialize symfony cache manager
        $cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter('web', 3600, root . '/Private/Cache');
        return $cache;
    },
    '_hybridauth' => function() {
        /** Uncomment code below to enable social oauth
         $instance = new Hybrid_Auth([
            'base_url' => App::$Alias->scriptUrl . '/api/user/endpoint?lang=en',
            'providers' => [
                'Twitter' => [
                    'enabled' => true,
                    'keys' => [
                        'key' => 'my_app_key',
                        'secret' => 'my_app_secret'
                    ],
                    'scope' => 'email'
                ],
                'Github' => [
                    'enabled' => true,
                    'keys' => [
                        'id' => 'my_client_id',
                        'secret' => 'my_client_secret'
                    ],
                    'wrapper' => [
                        'path' => root . '/vendor/hybridauth/hybridauth/additional-providers/hybridauth-github/Providers/GitHub.php',
                        'class' => 'Hybrid_Providers_GitHub'
                    ]
                ]
            ]
        ]);
        \App::$User->setOpenidInstance($instance);*/
    }
];