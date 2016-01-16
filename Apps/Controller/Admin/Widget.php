<?php

namespace Apps\Controller\Admin;


use Apps\Model\Admin\Application\FormInstall;
use Apps\Model\Admin\Application\FormTurn;
use Apps\Model\Admin\Application\FormUpdate;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Widget - control installed and not-installed widgets.
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

    /**
     * Show all installed widgets
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        return App::$View->render('index', [
            'widgets' => $this->widgets
        ]);
    }

    /**
     * Show installation form for widget
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionInstall()
    {
        $model = new FormInstall($this->applications, 'widget');

        // check if model is sended
        if ($model->send()) {
            // validate app name
            if ($model->validate()) {
                // try to run ::install method from remoute controller
                if ($model->make()) {
                    App::$Session->getFlashBag()->add('success', __('Widget "%widget%" is successful installed!', ['widget' => $model->sysname]));
                    App::$Response->redirect('widget/index');
                } else {
                    App::$Session->getFlashBag()->add('error', __('During the installation process an error has occurred! Please contact with widget developer.'));
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
     * Run widget update - display submit form & callback execution
     * @param $sys_name
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionUpdate($sys_name)
    {
        // get controller name and try to find app in db
        $controller = ucfirst(Str::lowerCase($sys_name));
        $search = \Apps\ActiveRecord\App::getItem('widget', $controller);

        // check what we got
        if ($search === null || (int)$search->id < 1) {
            throw new NotFoundException('Widget is not founded');
        }

        // init model and make update with notification
        $model = new FormUpdate($search);

        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Widget %w% is successful updated to %v% version', ['w' => $sys_name, 'v' => $model->scriptVersion]));
            App::$Response->redirect('application/index');
        }

        // render response
        return App::$View->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Allow turn on/off widget
     * @param $controllerName
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionTurn($controllerName)
    {
        $controllerName = ucfirst(Str::lowerCase($controllerName));

        $search = \Apps\ActiveRecord\App::where('sys_name', '=', $controllerName)->where('type', '=', 'widget')->first();

        if ($search === null || (int)$search->id < 1) {
            throw new ForbiddenException('App is not founded');
        }

        $model = new FormTurn();

        if ($model->send()) {
            $model->update($search);
            App::$Session->getFlashBag()->add('success', __('Widget status was changed'));
        }

        return App::$View->render('turn', [
            'widget' => $search,
            'model' => $model
        ]);
    }

}