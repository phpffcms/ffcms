<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Application\FormAppTurn;
use Apps\Model\Admin\Application\FormInstall;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Str;

class Application extends AdminController
{
    public $type = 'app';

    public function __construct()
    {
        // prevent version checks
        parent::__construct(false);
    }


    /**
     * List of all installed applications
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        return App::$View->render('index', [
            'apps' => $this->applications
        ]);
    }


    /**
     * Show installation for of applications
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionInstall()
    {
        $model = new FormInstall($this->table, 'app');

        // check if model is sended
        if ($model->send()) {
            // validate app name
            if ($model->validate()) {
                // try to run ::install method from remoute controller
                if ($model->make()) {
                        App::$Session->getFlashBag()->add('success', __('Application "%app%" is successful installed!', ['app' => $model->sysname]));
                    App::$Response->redirect('application/index');
                } else {
                    App::$Session->getFlashBag()->add('error', __('During the installation process an error has occurred! Please contact with application developer.'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Probably, app or widget with the same name is always used! Try to solve this conflict.'));
            }
        }

        return App::$View->render('install', [
            'model' => $model->export()
        ]);
    }

    /**
     * Allow turn on/off applications
     * @param $controllerName
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionTurn($controllerName)
    {
        $controllerName = ucfirst(Str::lowerCase($controllerName));

        $search = \Apps\ActiveRecord\App::where('sys_name', '=', $controllerName)->first();

        if ($search === null || (int)$search->id < 1) {
            throw new ForbiddenException();
        }

        $model = new FormAppTurn();

        if ($model->send()) {
            $model->updateApp($search);
            App::$Session->getFlashBag()->add('success', __('Application status was changed'));
        }

        return App::$View->render('turn', [
            'app' => $search,
            'model' => $model
        ]);
    }
}