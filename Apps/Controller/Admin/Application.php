<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Application\FormAppTurn;
use Extend\Core\Arch\AdminAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\String;

class Application extends AdminAppController
{
    // list of applications
    public function actionIndex()
    {
        $this->response = App::$View->render('index', [
            'apps' => $this->applications
        ]);
    }

    // allow turn on/off applications
    public function actionTurn($controller_name)
    {
        $controller_name = ucfirst(String::lowerCase($controller_name));

        $search = \Apps\ActiveRecord\App::where('sys_name', '=', $controller_name)->first();

        if ($search === null || (int)$search->id < 1) {
            throw new ForbiddenException();
        }

        $model = new FormAppTurn();

        if ($model->send()) {
            $model->updateApp($search);
            App::$Session->getFlashBag()->add('success', __('Application status was changed'));
        }

        $this->response = App::$View->render('turn', [
            'app' => $search,
            'model' => $model
        ]);
    }
}