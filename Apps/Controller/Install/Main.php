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
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionIndex()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new ForbiddenException('Installer is blocked! If you want to continue delete file /Private/Installer/install.lock');
        }

        $model = new EntityCheck();

        return App::$View->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Installation form
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
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

        return App::$View->render('install', [
            'model' => $model->filter()
        ]);
    }

    /**
     * Display view of success process finish
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionSuccess()
    {
        return App::$View->render('success');
    }
}