<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use Ffcms\Core\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

// define timezone
date_default_timezone_set('Europe/Moscow');

return [
    'Database' => function () {
        $capsule = new Capsule;
        if (env_name !== 'Install') {
            try {
                $capsule->addConnection(App::$Properties->get('database'));
            } catch (Exception $e) {
                exit('Database connection error!');
            }
        }

        $capsule->setAsGlobal(); // available from any places
        $capsule->bootEloquent(); // allow active record model's
        if (App::$Debug !== null) { // enable query collector
            $capsule->connection()->enableQueryLog();
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
        $swiftTransport = Swift_MailTransport::newInstance();
        return Swift_Mailer::newInstance($swiftTransport);
    },
    'Captcha' => function () {
        return new Extend\Core\Captcha\Gregwar();
    },
    'Cache' => function () {
        phpFastCache::setup('path', root . '/Private/Cache');
        return \phpFastCache();
    }
];