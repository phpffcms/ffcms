<?php

namespace Apps\Controller\Admin\Content;

use Apps\Model\Admin\Content\FormContentClear;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionClear
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionClear
{
    /**
     * Clear all trashed items
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function clear(): ?string
    {
        // find trashed rows
        $records = ContentEntity::onlyTrashed();

        // init model
        $model = new FormContentClear($records);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Trash content is cleaned'));
            $this->response->redirect('content/index');
        }

        // draw response
        return $this->view->render('content_clear', [
            'model' => $model
        ]);
    }
}
