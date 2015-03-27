<?php

namespace Controller;

use Core\Arch\Controller;

class Main extends Controller {

    public function before()
    {
        //self::$layout = 'other.php';
    }

    public function actionIndex()
    {
        $this->setGlobalVar('wtf', 'test global variable set');
        $this->response = \App::$View->render('index', ['t1' => 'test1', 't2' => 'test2']);
    }

    public function actionRead($id)
    {
        echo "Action read called" . $id;
    }
}