<?php

namespace Apps\Controller\Admin;

use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Arch\View;

class Main extends Controller
{

    public function actionIndex()
    {
        $this->response = (new View('Main', 'index'))->out([

        ]);
    }
}