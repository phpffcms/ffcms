<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Ffcms\Core\App;

// establish database link
$capsule = new Capsule;
$capsule->addConnection(App::$Property->get('database'));
$capsule->setAsGlobal(); // available from any places
$capsule->bootEloquent(); // allow active record model's


return [
    'Session' => new Ffcms\Core\Session\DefaultSession([
        'lifetime' => 604800, // 1 week
        'path' => root . '/Private/Sessions'
    ]),
    'User' => new Apps\Model\Basic\User()
];