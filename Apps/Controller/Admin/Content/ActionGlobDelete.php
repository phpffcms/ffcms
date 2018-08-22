<?php

namespace Apps\Controller\Admin\Content;

use Apps\Model\Admin\Content\FormContentGlobDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionGlobDelete
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionGlobDelete
{
    /**
     * Show content global delete
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function globDelete(): ?string
    {
        // get content ids from request
        $ids = $this->request->query->get('selected');

        // check if input is array
        if (!Any::isArray($ids) || count($ids) < 1) {
            throw new NotFoundException(__('Nothing to delete is founded'));
        }

        // get all records as object from db
        $records = ContentEntity::find($ids);
        if ($records->count() < 1) {
            throw new NotFoundException(__('Nothing to delete is founded'));
        }

        // init model and pass objects
        $model = new FormContentGlobDelete($records);

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Content are successful removed'));
            $this->response->redirect('content/index');
        }

        // return response
        return $this->view->render('content/content_glob_delete', [
            'model' => $model
        ]);
    }
}
