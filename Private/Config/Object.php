<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use Ffcms\Core\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

// define timezone
date_default_timezone_set('Europe/Moscow');

return [
    'Session' => function() {
        $handler = new NativeFileSessionHandler(root . '/Private/Sessions');
        $storage = new NativeSessionStorage([
            'cookie_lifetime' => 604800,
            'gc_maxlifetime' => 604800,
            'cookie_httponly' => '1'
        ], $handler);

        return new Session($storage);
    },
    'User' => function () {
        return new Apps\ActiveRecord\User();
    },
    'Database' => function () {
        $capsule = new Capsule;
        $capsule->addConnection(App::$Properties->get('database'));
        $capsule->setAsGlobal(); // available from any places
        $capsule->bootEloquent(); // allow active record model's

        return $capsule;
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