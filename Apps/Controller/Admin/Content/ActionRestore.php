<?php

namespace Apps\Controller\Admin\Content;

use Apps\Model\Admin\Content\FormContentRestore;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionRestore
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRestore
{
    /**
     * Restore deleted content
     * @param $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function restore(string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException();
        }

        // get removed object
        $record = ContentEntity::onlyTrashed()->find($id);
        if (!$record) {
            throw new NotFoundException();
        }

        // init model
        $model = new FormContentRestore($record);
        // check if action is send
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Content are successful recovered'));
            $this->response->redirect('content/index');
        }

        // draw response
        return $this->view->render('content/content_restore', [
            'model' => $model
        ]);
    }
}
