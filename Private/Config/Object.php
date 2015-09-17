<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Ffcms\Core\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

// define timezone
date_default_timezone_set('Europe/Moscow');

// establish database link in laravel Active Record
$capsule = new Capsule;
$capsule->addConnection(App::$Property->get('database'));
$capsule->setAsGlobal(); // available from any places
$capsule->bootEloquent(); // allow active record model's

// establish swift mailer
$swiftTransport = Swift_MailTransport::newInstance();

// configure cache
phpFastCache::setup('path', root . '/Private/Cache');

return [
    'Session' => new Session(new NativeSessionStorage(
        [
            'cookie_lifetime' => 604800,
            'gc_maxlifetime' => 604800,
            'cookie_httponly' => '1'
        ],
        new NativeFileSessionHandler(root . '/Private/Sessions')
    )),
    'User' => new Apps\ActiveRecord\User(),
    'Database' => $capsule,
    'Mailer' => Swift_Mailer::newInstance($swiftTransport),
    'Captcha' => new Extend\Core\Captcha\Gregwar(),
    //'Captcha' => new Extend\Core\Captcha\Recaptcha('siteKey', 'secret')
    'Cache' => \phpFastCache()
];