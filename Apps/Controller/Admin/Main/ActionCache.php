<?php

namespace Apps\Controller\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionCache
 * @package Apps\Controller\Admin\Main
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionCache
{
    /**
     * Clear cache data if submited
     * @return string|null
     */
    public function cache(): ?string
    {
        // check if submited
        if ($this->request->request->get('clearcache', false)) {
            // clear cache
            App::$Cache->clear();
            // add notification & redirect
            App::$Session->getFlashBag()->add('success', __('Cache cleared successfully'));
            $this->response->redirect('/');
        }

        // render output view
        return $this->view->render('main/clear_cache');
    }
}