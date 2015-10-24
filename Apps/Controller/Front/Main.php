<?php

namespace Apps\Controller\Front;

use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\Arch\View;
use Apps\Model\Front\User;


class Main extends FrontAppController
{

    public function before()
    {
        //self::$layout = 'other.php';
    }

    public function actionIndex()
    {
        $this->wtf = 'Test me baby ;)';
        //$this->setGlobalVar('wtf', 'test global variable set');

        $this->response = \App::$View->render('index', ['t1' => 'test1', 't2' => 'test2']);
    }

    public function actionRead($id)
    {
        echo "Action read called" . $id;
    }
}