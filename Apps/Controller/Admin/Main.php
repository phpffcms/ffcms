<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Main\EntityDeleteRoute;
use Apps\Model\Admin\Main\FormAddRoute;
use Apps\Model\Admin\Main\FormSettings;
use Extend\Core\Arch\AdminAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Integer;
use Ffcms\Core\Helper\Type\Str;

class Main extends AdminAppController
{
    public function __construct()
    {
        parent::__construct(false);
    }

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

    /**
     * List available routes
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRouting()
    {
        $routingMap = App::$Properties->getAll('Routing');

        $this->response = App::$View->render('routing', [
            'routes' => $routingMap
        ]);
    }

    /**
     * Show add form for routing
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionAddroute()
    {
        $model = new FormAddRoute();

        if (!File::exist('/Private/Config/Routing.php') || !File::writable('/Private/Config/Routing.php')) {
            App::$Session->getFlashBag()->add('error', __('Routing configuration file is not allowed to write: /Private/Config/Routing.php'));
        } elseif ($model->send() && $model->validate()) {
            $model->save();
            App::$Response->redirect('main/routing');
        }

        $this->response = App::$View->render('add_route', [
            'model' => $model
        ]);
    }

    /**
     * Delete scheme route
     * @throws SyntaxException
     */
    public function actionDeleteroute()
    {
        $type = (string)App::$Request->query->get('type');
        $loader = (string)App::$Request->query->get('loader');
        $source = Str::lowerCase((string)App::$Request->query->get('path'));

        $model = new EntityDeleteRoute($type, $loader, $source);
        if ($model->send()) {
            $model->make();
            App::$Response->redirect('main/routing');
        }

        $this->response = App::$View->render('delete_route', [
            'model' => $model
        ]);
    }
}