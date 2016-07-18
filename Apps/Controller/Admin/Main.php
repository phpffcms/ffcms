<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Session;
use Apps\Model\Admin\Main\EntityDeleteRoute;
use Apps\Model\Admin\Main\FormAddRoute;
use Apps\Model\Admin\Main\FormSettings;
use Apps\Model\Install\Main\EntityCheck;
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
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionIndex()
    {
        // cache some data
        $rootSize = App::$Cache->get('root.size');
        if ($rootSize === null) {
            $rootSize = round(Directory::getSize('/') / (1024*1000), 2) . ' mb';
            App::$Cache->set('root.size', $rootSize, 86400); // 24 hours caching = 60 * 60 * 24
        }
        $loadAvg = App::$Cache->get('load.average');
        if ($loadAvg === null) {
            $loadAvg = Environment::loadAverage();
            App::$Cache->set('load.average', $loadAvg, 60*5); // 5 min cache
        }

        // prepare system statistic
        $stats = [
            'ff_version' => App::$Properties->version['num'] . ' (' . App::$Properties->version['date'] . ')',
            'php_version' => Environment::phpVersion() . ' (' . Environment::phpSAPI() . ')',
            'os_name' => Environment::osName(),
            'database_name' => App::$Database->connection()->getDatabaseName() . ' (' . App::$Database->connection()->getDriverName() . ')',
            'file_size' => $rootSize,
            'load_avg' => $loadAvg
        ];
        // check directory chmods and other environment features
        $model = new EntityCheck();

        // render view output
        return $this->view->render('index', [
            'stats' => $stats,
            'check' => $model
        ]);
    }

    /**
     * Manage settings in web
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionSettings()
    {
        // init settings model and process post send
        $model = new FormSettings(true);
        if ($model->send()) {
            if ($model->validate()) {
                if ($model->makeSave()) {
                    // show message about successful save and take system some time ;)
                    return $this->view->render('settings_save');
                } else {
                    App::$Session->getFlashBag()->add('error', __('Configuration file is not writable! Check /Private/Config/ dir and files'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Validation of form data is failed!'));
            }
        }

        // render output view
        return $this->view->render('settings', [
            'model' => $model->filter()
        ]);
    }

    /**
     * Manage files via elFinder
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionFiles()
    {
        return $this->view->render('files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . $this->request->getLanguage()
        ]);
    }

    /**
     * Show antivirus view
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionAntivirus()
    {
        return $this->view->render('antivirus');
    }

    /**
     * Set debugging cookie to current user session
     */
    public function actionDebugcookie()
    {
        $cookieProperty = App::$Properties->get('debug');
        //$this->request->cookies->add([$cookieProperty['cookie']['key'] => $cookieProperty['cookie']['value']]); todo: fix me
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], Integer::MAX, '/', null, null, true);
        $this->response->redirect('/');
    }

    /**
     * List available routes
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionRouting()
    {
        $routingMap = App::$Properties->getAll('Routing');

        return $this->view->render('routing', [
            'routes' => $routingMap
        ]);
    }

    /**
     * Show add form for routing
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionAddroute()
    {
        $model = new FormAddRoute(true);
        
        if (!File::exist('/Private/Config/Routing.php') || !File::writable('/Private/Config/Routing.php')) {
            App::$Session->getFlashBag()->add('error', __('Routing configuration file is not allowed to write: /Private/Config/Routing.php'));
        } elseif ($model->send() && $model->validate()) {
            $model->save();
            return $this->view->render('add_route_save');
        }

        return $this->view->render('add_route', [
            'model' => $model->filter()
        ]);
    }

    /**
     * Delete scheme route
     * @throws SyntaxException
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionDeleteroute()
    {
        $type = (string)$this->request->query->get('type');
        $loader = (string)$this->request->query->get('loader');
        $source = Str::lowerCase((string)$this->request->query->get('path'));

        $model = new EntityDeleteRoute($type, $loader, $source);
        if ($model->send() && $model->validate()) {
            $model->make();
            return $this->view->render('delete_route_save');
        }

        return $this->view->render('delete_route', [
            'model' => $model->filter()
        ]);
    }

    /**
     * Clear cached data
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws SyntaxException
     */
    public function actionCache()
    {
        $stats = App::$Cache->stats();
        // get size in mb from cache stats
        $size = round((int)$stats['size'] / (1024*1024), 2);

        // check if submited
        if ($this->request->request->get('clearcache', false)) {
            // clear cache
            App::$Cache->clean();
            // add notification & redirect
            App::$Session->getFlashBag()->add('success', __('Cache cleared successfully'));
            $this->response->redirect('/');
        }

        // render output view
        return $this->view->render('clear_cache', [
            'size' => $size
        ]);
    }

    /**
     * Clear all sessions data
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws SyntaxException
     */
    public function actionSessions()
    {
        // get all sessions data
        $sessions = Session::all();

        // check if action is submited
        if ($this->request->request->get('clearsessions', false)) {
            // truncate table
            App::$Database->table('sessions')->truncate();
            // add notification and make redirect to main
            App::$Session->getFlashBag()->add('success', __('Sessions cleared successfully'));
            $this->response->redirect('/');
        }

        // render output view
        return $this->view->render('clear_sessions', [
            'count' => $sessions->count()
        ]);
    }
}