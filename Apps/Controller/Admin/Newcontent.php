<?php

namespace Apps\Controller\Admin;

use Ffcms\Core\App;
use Extend\Core\Arch\AdminController;
use Apps\Model\Admin\Newcontent\FormSettings;

/**
 * Admin controller of new content widget.
 */
class Newcontent extends AdminController
{
    const VERSION = 0.1;
    
    public $type = 'widget';

    /**
     * Show widget settings
     * @return string
     */
    public function actionIndex()
    {
        // init settings model
        $model = new FormSettings($this->getConfigs());
        
        // check if request is submited
        if ($model->send() && $model->validate()) {
            $this->setConfigs($model->getResult());
            App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
        }
        
        // render viewer
        return App::$View->render('index', [
            'model' => $model->export()
        ]);
    }
}