<?php

namespace Apps\Controller\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDebugCookie
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDebugCookie
{
    /**
     * Set debug cookies for current user
     * @return void
     */
    public function debugCookie()
    {
        $cookieProperty = App::$Properties->get('debug');
        setcookie($cookieProperty['cookie']['key'], $cookieProperty['cookie']['value'], strtotime('+1 month'), '/', null, null, true);
        $this->response->redirect('/');
    }
}