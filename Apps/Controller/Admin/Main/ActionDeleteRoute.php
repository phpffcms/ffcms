<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\EntityDeleteRoute;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDeleteRoute
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDeleteRoute
{
    /**
     * Delete existing route action
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function deleteRoute(): ?string
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
}