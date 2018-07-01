<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Main\EntityDeleteRoute;
use Apps\Model\Admin\Main\FormAddRoute;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Main. Admin main controller - index page, settings, file manager, security and etc.
 * @package Apps\Controller\Admin
 */
class Main extends AdminController
{
    public $type = 'app';

    // import heavy actions
    use Main\ActionIndex {
        index as actionIndex;
    }

    use Main\ActionSettings {
        settings as actionSettings;
    }

    use Main\ActionUpdates {
        updates as actionUpdates;
    }

    use Main\ActionSessions {
        sessions as actionSessions;
    }

    /**
     * Main constructor. Disable parent inheritance of typical app version checking
     */
    public function __construct()
    {
        parent::__construct(false);
    }

    /**
     * Manage files via elFinder
     * @return string
     */
    public function actionFiles(): ?string
    {
        return $this->view->render('main/files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . $this->request->getLanguage()
        ]);
    }

    /**
     * Show antivirus view
     * @return string
     */
    public function actionAntivirus(): ?string
    {
        return $this->view->render('main/antivirus');
    }

    /**
     * Set debugging cookie to current user session
     */
    public function actionDebugcookie(): void
    {
        $cookieProperty = App::$Properties->get('debug');
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], strtotime('+1 month'), '/', null, null, true);
        $this->response->redirect('/');
    }

    /**
     * List available routes
     * @return string
     * @throws SyntaxException
     */
    public function actionRouting(): ?string
    {
        $routingMap = App::$Properties->getAll('Routing');

        return $this->view->render('main/routing', [
            'routes' => $routingMap
        ]);
    }

    /**
     * Show add form for routing
     * @return string
     * @throws SyntaxException
     */
    public function actionAddroute(): ?string
    {
        $model = new FormAddRoute(true);
        
        if (!File::exist('/Private/Config/Routing.php') || !File::writable('/Private/Config/Routing.php')) {
            App::$Session->getFlashBag()->add('error', __('Routing configuration file is not allowed to write: /Private/Config/Routing.php'));
        } elseif ($model->send() && $model->validate()) {
            $model->save();
            return $this->view->render('add_route_save');
        }

        return $this->view->render('main/add_route', [
            'model' => $model
        ]);
    }

    /**
     * Delete scheme route
     * @throws SyntaxException
     * @return string
     */
    public function actionDeleteroute(): ?string
    {
        $type = (string)$this->request->query->get('type');
        $loader = (string)$this->request->query->get('loader');
        $source = Str::lowerCase((string)$this->request->query->get('path'));

        $model = new EntityDeleteRoute($type, $loader, $source);
        if ($model->send() && $model->validate()) {
            $model->make();
            return $this->view->render('main/delete_route_save');
        }

        return $this->view->render('main/delete_route', [
            'model' => $model
        ]);
    }

    /**
     * Clear cached data
     * @return string
     * @throws SyntaxException
     */
    public function actionCache(): ?string
    {
        // check if submited
        if ($this->request->request->get('clearcache', false)) {
            // clear cache
            App::$Cache->clean();
            // add notification & redirect
            App::$Session->getFlashBag()->add('success', __('Cache cleared successfully'));
            $this->response->redirect('/');
        }

        // render output view
        return $this->view->render('main/clear_cache', []);
    }
}
