<?php

namespace Apps\Controller\Admin;

use Ffcms\Core\App;
use Extend\Core\Arch\AdminController;
use Apps\Model\Admin\Newcomment\FormSettings;

class Newcomment extends AdminController
{
    const VERSION = 0.1;
    
    public $type = 'widget';
    
    public function actionIndex()
    {
        $model = new FormSettings($this->getConfigs());
        
        if ($model->send() && $model->validate()) {
            $this->setConfigs($model->getAllProperties());
            App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
        }
        
        return App::$View->render('index', [
            'model' => $model->export()
        ]);
    }
}