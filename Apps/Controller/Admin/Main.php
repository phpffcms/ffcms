<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Session;
use Apps\Model\Admin\Main\EntityDeleteRoute;
use Apps\Model\Admin\Main\EntityUpdate;
use Apps\Model\Admin\Main\FormAddRoute;
use Apps\Model\Admin\Main\FormSettings;
use Apps\Model\Admin\Main\FormUpdateDatabase;
use Apps\Model\Admin\Main\FormUpdateDownload;
use Apps\Model\Install\Main\EntityCheck;
use Extend\Core\Arch\AdminController;
use Extend\Version;
use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Environment;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/**
 * Class Main. Admin main controller - index page, settings, file manager, security and etc.
 * @package Apps\Controller\Admin
 */
class Main extends AdminController
{
    public $type = 'app';

    /**
     * Main constructor. Disable parent inheritance of typical app version checking
     */
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
        // get cached statistics
        $rootSize = App::$Cache->getItem('root.size');
        $loadAvg = App::$Cache->getItem('load.avarage');
        if (!$rootSize->isHit()) {
            $calcSize = round(Directory::size('/') / (1024*1000), 2) . ' mb';
            $rootSize->set($calcSize);
            $rootSize->expiresAfter(86400);
            App::$Cache->save($rootSize);
        }
        if (!$loadAvg->isHit()) {
            $loadAvg->set(Environment::loadAverage());
            $loadAvg->expiresAfter(300);
            App::$Cache->save($loadAvg);
        }

        // prepare system statistic
        $stats = [
            'ff_version' => Version::VERSION . ' (' . Version::DATE . ')',
            'php_version' => Environment::phpVersion() . ' (' . Environment::phpSAPI() . ')',
            'os_name' => Environment::osName(),
            'database_name' => App::$Database->connection()->getDatabaseName() . ' (' . App::$Database->connection()->getDriverName() . ')',
            'file_size' => $rootSize->get(),
            'load_avg' => $loadAvg->get()
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
            'model' => $model
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
        // awesome bullshit in symfony: you can't set cookie headers in (new RedirectResponse) before send().
        // never mind what did you do, this is a easy way to do this without bug
        //$this->response->headers->setCookie(new Cookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], strtotime('+1 month'), null, false, true));
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], strtotime('+1 month'), '/', null, null, true);
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
            'model' => $model
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
            'model' => $model
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

    /**
     * Make system update
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionUpdates()
    {
        // initialize models - entity, database, download
        $entityModel = new EntityUpdate();
        $dbModel = new FormUpdateDatabase($entityModel->dbVersion, $entityModel->scriptVersion);
        $downloadModel = new FormUpdateDownload($entityModel->lastInfo['download_url'], $entityModel->lastVersion);

        // find files with sql queries to update if required
        if (!$entityModel->versionsEqual) {
            $dbModel->findUpdateFiles();
            // if submit is pressed make update
            if ($dbModel->send() && $dbModel->validate()) {
                $dbModel->make();
                App::$Session->getFlashBag()->add('success', __('Database updates are successful installed'));
                App::$Response->redirect(Url::to('main/updates'));
            }
        } elseif ($entityModel->haveRemoteNew) { // download full compiled .zip archive & extract files
            if ($downloadModel->send()) {
                if ($downloadModel->make()) {
                    App::$Session->getFlashBag()->add('success', __('Archive with new update are successful downloaded and extracted. Please refresh this page and update database if required'));
                } else {
                    App::$Session->getFlashBag()->add('error', __('In process of downloading and extracting update archive error is occurred. Something gonna wrong'));
                }
            }
        }

        return $this->view->render('updates', [
            'entityModel' => $entityModel,
            'dbModel' => $dbModel,
            'downloadModel' => $downloadModel
        ]);
    }
}