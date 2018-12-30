<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\FormAddRoute;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionAddRoute
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionAddRoute
{
    /**
     * Route add form action
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function addRoute(): ?string
    {
        $model = new FormAddRoute(true);

        if (!File::exist('/Private/Config/Routing.php') || !File::writable('/Private/Config/Routing.php')) {
            App::$Session->getFlashBag()->add('error', __('Routing configuration file is not allowed to write: /Private/Config/Routing.php'));
        } elseif ($model->send() && $model->validate()) {
            $model->save();
            return $this->view->render('main/add_route_save');
        }

        return $this->view->render('main/add_route', [
            'model' => $model
        ]);
    }
}