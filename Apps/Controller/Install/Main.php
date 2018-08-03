<?php

namespace Apps\Controller\Install;

use Apps\Model\Install\Main\EntityCheck;
use Apps\Model\Install\Main\FormInstall;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\File;

/**
 * Class Main. Controller for install & update
 * @package Apps\Controller\Install
 */
class Main extends Controller
{

    /**
     * Silent hide all installer output if final.lock exists
     * @throws NativeException
     */
    public function before()
    {
        if (File::exist('/Private/Install/final.lock')) {
            throw new NativeException('Page is not founded!');
        }
    }

    /**
     * Show environment check form
     * @throws ForbiddenException
     */
    public function actionIndex()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new ForbiddenException(__('installer is blocked! If you want to continue delete file /Private/Installer/install.lock'));
        }

        $model = new EntityCheck();

        return $this->view->render('main/index', [
            'model' => $model
        ]);
    }

    /**
     * Display installation form and process install
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionInstall()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new ForbiddenException(__('installer is blocked! If you want to continue delete file /Private/Installer/install.lock'));
        }

        $model = new FormInstall();
        if ($model->send() && $model->validate()) {
            $model->make();
            $this->response->redirect('main/success');
        }

        return $this->view->render('main/install', [
            'model' => $model
        ]);
    }

    /**
     * Display view of success process finish
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSuccess()
    {
        return $this->view->render('main/success');
    }
}
