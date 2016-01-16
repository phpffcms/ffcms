<?php

namespace Apps\Controller\Admin;


use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Comments - manage user comments.
 * This class provide general admin implementation of control for user comments and its settings.
 * @package Apps\Controller\Admin
 */
class Comments extends AdminController
{
    const VERSION = 0.1;
    public $type = 'widget';

    public function actionIndex()
    {
        return App::$View->render('index', [

        ]);
    }




}