<?php

namespace Apps\Controller\Admin;


use Apps\ActiveRecord\Session;
use Apps\Model\Admin\Comments\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Comments. Admin controller for management user comments.
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

    public function actionSettings()
    {
        // initialize settings model
        $model = new FormSettings($this->getConfigs());

        // check if form is send
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                App::$Response->redirect('comments/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return App::$View->render('settings', [
            'model' => $model
        ]);
    }




}