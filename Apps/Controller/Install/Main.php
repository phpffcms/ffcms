<?php

namespace Apps\Controller\Install;


use Apps\Model\Install\Main\EntityCheck;
use Apps\Model\Install\Main\FormInstall;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\File;

class Main extends Controller
{

    public function before()
    {
        if (File::exist('/Private/Install/final.lock')) {
            throw new NativeException('Page is not founded!');
        }
    }

    /**
     * Show environment check form
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new ForbiddenException('Installer is blocked! If you want to continue delete file /Private/Installer/install.lock');
        }

        $model = new EntityCheck();

        $this->response = App::$View->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Installation form
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionInstall()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new ForbiddenException('Installer is blocked! If you want to continue delete file /Private/Installer/install.lock');
        }

        $model = new FormInstall();
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Response->redirect('main/success');
        }

        $this->response = App::$View->render('install', [
            'model' => $model->export()
        ]);
    }

    /**
     * Display view of success process finish
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSuccess()
    {
        $this->response = App::$View->render('success');
    }
}