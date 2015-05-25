<?php

namespace Apps\Controller\Admin;

use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

class Main extends AdminController
{

    public function actionIndex()
    {
        $this->response = App::$View->render('index', [

        ]);
    }
}