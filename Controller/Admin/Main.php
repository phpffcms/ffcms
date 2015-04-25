<?php

namespace Controller\Admin;

use Core\Arch\Controller;
use Core\Arch\View;

class Main extends Controller
{

    public function actionIndex()
    {
        $this->response = (new View('Main', 'index'))->out([

        ]);
    }
}