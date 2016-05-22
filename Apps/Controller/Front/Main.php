<?php

namespace Apps\Controller\Front;

use Extend\Core\Arch\Controller;
use Apps\Model\Front\User;
use Ffcms\Core\App;


class Main extends Controller
{

    public function before()
    {
        //self::$layout = 'other.php';
    }

    public function actionIndex()
    {
        $this->wtf = 'Test global variable';

        return App::$View->render('index', ['t1' => 'test1', 't2' => 'test2']);
    }

    public function actionRead($id)
    {
        echo "Action read called" . $id;
    }
}