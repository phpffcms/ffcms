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
 * Class Widget. Control installed and not-installed widgets.
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
        return $this->view->render('index', [
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
                    $this->response->redirect('widget/index');
                } else {
                    App::$Session->getFlashBag()->add('error', __('During the installation process an error has occurred! Please contact with widget developer.'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Probably, app or widget with the same name is always used! Try to solve this conflict.'));
            }
        }

        return $this->view->render('install', [
            'model' => $model
        ]);
    }

    /**
     * Run widget update - display submit form & callback execution
     * @param string $sys_name
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
            $this->response->redirect('application/index');
        }

        // render response
        return $this->view->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Allow turn on/off widget
     * @param string $controllerName
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionTurn($controllerName)
    {
        // get controller name & find object in db
        $controllerName = ucfirst(Str::lowerCase($controllerName));
        $record = \Apps\ActiveRecord\App::where('sys_name', '=', $controllerName)->where('type', '=', 'widget')->first();

        // check if widget admin controller exists
        if ($record === null || (int)$record->id < 1) {
            throw new ForbiddenException('Widget is not founded');
        }

        // initialize turn on/off model
        $model = new FormTurn($record);
        if ($model->send()) {
            $model->update();
            App::$Session->getFlashBag()->add('success', __('Widget status was changed'));
        }

        // render view
        return $this->view->render('turn', [
            'widget' => $record,
            'model' => $model
        ]);
    }
}
