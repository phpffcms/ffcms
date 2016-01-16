<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Application\FormTurn;
use Apps\Model\Admin\Application\FormInstall;
use Apps\Model\Admin\Application\FormUpdate;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
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
        $model = new FormInstall($this->applications, 'app');

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
    
    public function actionUpdate($sys_name)
    {
        // get controller name and try to find app in db
        $controller = ucfirst(Str::lowerCase($sys_name));
        $search = \Apps\ActiveRecord\App::getItem('app', $controller);

        // check what we got
        if ($search === null || (int)$search->id < 1) {
            throw new NotFoundException('App is not founded');
        }

        // init model and make update with notification
        $model = new FormUpdate($search);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Application %s% is successful updated to %v% version', ['s' => $sys_name, 'v' => $model->scriptVersion]));
            App::$Response->redirect('application/index');
        }

        // render response
        return App::$View->render('update', [
            'model' => $model
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

        $search = \Apps\ActiveRecord\App::where('sys_name', '=', $controllerName)->where('type', '=', 'app')->first();

        if ($search === null || (int)$search->id < 1) {
            throw new ForbiddenException('App is not founded');
        }

        $model = new FormTurn();

        if ($model->send()) {
            $model->update($search);
            App::$Session->getFlashBag()->add('success', __('Application status was changed'));
        }

        return App::$View->render('turn', [
            'app' => $search,
            'model' => $model
        ]);
    }
}