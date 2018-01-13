<?php

namespace Apps\Controller\Admin\Main;

use Apps\ActiveRecord\Session;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSessions
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSessions
{
    /**
     * Clear all sessions data
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function sessions()
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
