<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Main\EntityDeleteRoute;
use Apps\Model\Admin\Main\FormAddRoute;
use Apps\Model\Admin\Main\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Environment;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Integer;
use Ffcms\Core\Helper\Type\Str;

class Main extends AdminController
{
    public $type = 'app';

    public function __construct()
    {
        parent::__construct(false);
    }

    /**
     * Index page of admin dashboard
     * @return string
     */
    public function actionIndex()
    {
        // cache some data
        $rootSize = App::$Cache->get('root.size');
        if ($rootSize === null) {
            $rootSize = round(Directory::getSize('/') / (1024*1000), 2) . ' mb';
            App::$Cache->set('root.size', $rootSize, 60 * 60 * 24); // 24 hours caching
        }
        $loadAvg = App::$Cache->get('load.average');
        if ($loadAvg === null) {
            $loadAvg = Environment::loadAverage();
            App::$Cache->set('load.average', $loadAvg, 60*5); // 5 min cache
        }

        $stats = [
            'ff_version' => App::$Properties->version['num'] . ' (' . App::$Properties->version['date'] . ')',
            'php_version' => Environment::phpVersion() . ' (' . Environment::phpSAPI() . ')',
            'os_name' => Environment::osName(),
            'database_name' => App::$Database->connection()->getDatabaseName() . ' (' . App::$Database->connection()->getDriverName() . ')',
            'file_size' => $rootSize,
            'load_avg' => $loadAvg
        ];

        return App::$View->render('index', [
            'stats' => $stats
        ]);
    }

    /**
     * Manage settings in web
     * @return string
     */
    public function actionSettings()
    {
        $model = new FormSettings();

        if ($model->send()) {
            if ($model->validate()) {
                if ($model->makeSave()) {
                    // show message about successful save and take system some time ;)
                    return App::$View->render('settings_save');
                } else {
                    App::$Session->getFlashBag()->add('error', __('Configuration file is not writable! Check /Private/Config/ dir and files'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Validation of form data is failed!'));
            }
        }

        return App::$View->render('settings', [
            'model' => $model // no $model->export() there
        ]);
    }

    /**
     * Manage files via elFinder
     * @return string
     */
    public function actionFiles()
    {
        return App::$View->render('files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . App::$Request->getLanguage()
        ]);
    }

    /**
     * Show antivirus
     * @return string
     */
    public function actionAntivirus()
    {
        return App::$View->render('antivirus');
    }

    /**
     * Set debugging cookie to current user session
     */
    public function actionDebugcookie()
    {
        $cookieProperty = App::$Properties->get('debug');
        //App::$Request->cookies->add([$cookieProperty['cookie']['key'] => $cookieProperty['cookie']['value']]); todo: fix me
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], Integer::MAX, '/', null, null, true);
        App::$Response->redirect('/');
    }

    /**
     * List available routes
     * @return string
     */
    public function actionRouting()
    {
        $routingMap = App::$Properties->getAll('Routing');

        return App::$View->render('routing', [
            'routes' => $routingMap
        ]);
    }

    /**
     * Show add form for routing
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @return string
     */
    public function actionAddroute()
    {
        $model = new FormAddRoute();

        if (!File::exist('/Private/Config/Routing.php') || !File::writable('/Private/Config/Routing.php')) {
            App::$Session->getFlashBag()->add('error', __('Routing configuration file is not allowed to write: /Private/Config/Routing.php'));
        } elseif ($model->send() && $model->validate()) {
            $model->save();
            return App::$View->render('add_route_save');
        }

        return App::$View->render('add_route', [
            'model' => $model
        ]);
    }

    /**
     * Delete scheme route
     * @throws SyntaxException
     * @return string
     */
    public function actionDeleteroute()
    {
        $type = (string)App::$Request->query->get('type');
        $loader = (string)App::$Request->query->get('loader');
        $source = Str::lowerCase((string)App::$Request->query->get('path'));

        $model = new EntityDeleteRoute($type, $loader, $source);
        if ($model->send()) {
            $model->make();
            return App::$View->render('delete_route_save');
        }

        return App::$View->render('delete_route', [
            'model' => $model
        ]);
    }
}