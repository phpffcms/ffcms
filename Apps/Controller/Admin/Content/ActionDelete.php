<?php

namespace Apps\Controller\Admin\Content;

use Apps\Model\Admin\Content\FormContentDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionDelete
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDelete
{
    /**
     * Delete content by id
     * @param string $id
     * @return string
     * @throws \Exception
     */
    public function delete(string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException();
        }

        // get content record and check availability
        $record = ContentEntity::find($id);
        if (!$record) {
            throw new NotFoundException();
        }

        // init delete model
        $model = new FormContentDelete($record);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Content is successful moved to trash'));
            $this->response->redirect('content/index');
        }

        return $this->view->render('content/content_delete', [
            'model' => $model
        ]);
    }
}
