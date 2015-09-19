<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Main\FormSettings;
use Extend\Core\Arch\AdminAppController;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Type\Integer;

class Main extends AdminAppController
{

    /**
     * Index page of admin dashboard
     */
    public function actionIndex()
    {
        $this->response = App::$View->render('index', [

        ]);
    }

    /**
     * Manage settings in web
     */
    public function actionSettings()
    {
        $model = new FormSettings();

        if ($model->send()) {
            if ($model->validate()) {
                if ($model->makeSave()) {
                    // show message about successful save and take system some time ;)
                    $this->response = App::$View->render('settings_save');
                    return;
                } else {
                    App::$Session->getFlashBag()->add('error', __('Configuration file is not writable! Check /Private/Config/ dir and files'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Validation of form data is failed!'));
            }
        }

        $this->response = App::$View->render('settings', [
            'model' => $model // no $model->export() there
        ]);
    }

    /**
     * Manage files via elFinder
     */
    public function actionFiles()
    {
        $this->response = App::$View->render('files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . App::$Request->getLanguage()
        ]);
    }

    /**
     * Show antivirus
     */
    public function actionAntivirus()
    {
        $this->response = App::$View->render('antivirus');
    }

    public function actionDebugcookie()
    {
        $cookieProperty = App::$Properties->get('debug');
        //App::$Request->cookies->add([$cookieProperty['cookie']['key'] => $cookieProperty['cookie']['value']]); todo: fix me
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], Integer::MAX, '/', null, null, true);
        App::$Response->redirect('/');
    }
}