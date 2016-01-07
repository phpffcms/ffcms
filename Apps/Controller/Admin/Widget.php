<?php

namespace Apps\Controller\Admin;


use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Widget - control of user comments in website.
 * This class provide general admin implementation of control for user comments and its settings
 * @package Apps\Controller\Admin
 */
class Widget extends AdminController
{
    public $type = 'widget';

    /**
     * Widget constructor. Disable installation checking for this controller
     */
    public function __construct()
    {
        parent::__construct(false);
    }

    // list all user comments
    public function actionIndex()
    {
        return App::$View->render('index', [
            'widgets' => $this->widgets
        ]);
    }

}