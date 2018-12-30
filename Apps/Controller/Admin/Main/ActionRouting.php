<?php

namespace Apps\Controller\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionRouting
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRouting
{
    /**
     * Display routing map
     * @return string|null
     */
    public function routing(): ?string
    {
        return $this->view->render('main/routing', [
            'routes' => App::$Properties->getAll('Routing')
        ]);
    }
}