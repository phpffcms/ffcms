<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Contenttag\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Contenttag. Admin controller of widget content tags
 * @package Apps\Controller\Admin
 */
class Contenttag extends AdminController
{
    const VERSION = '1.0.0';
    
    public $type = 'widget';

    /**
     * Show and edit widget settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionIndex()
    {
        // initialize model and pass configs as arg
        $model = new FormSettings($this->getConfigs());
        
        // check if form of depend model is submited
        if ($model->send() && $model->validate()) {
            $this->setConfigs($model->getAllProperties());
            App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
        }
        
        // render view output
        return $this->view->render('index', [
           'model' => $model
        ]);
    }
}