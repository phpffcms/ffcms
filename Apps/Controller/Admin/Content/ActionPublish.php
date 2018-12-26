<?php

namespace Apps\Controller\Admin\Content;

use Apps\ActiveRecord\Content as ContentEntity;
use Apps\Model\Admin\Content\FormContentPublish;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionPublish
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionPublish
{
    /**
     * Publish content on moderate stage
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function publish(): ?string
    {
        // get ids as array from GET
        $ids = $this->request->query->get('selected');
        if (!Any::isArray($ids) || count($ids) < 1) {
            throw new NotFoundException(__('Items to publish is not found'));
        }

        // try to find items in db
        $records = ContentEntity::whereIn('id', $ids)->where('display', 0);
        if ($records->count() < 1) {
            throw new NotFoundException(__('Items to publish is not found'));
        }

        // initialize model and operate submit
        $model = new FormContentPublish($records);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Content is successful published'));
            $this->response->redirect('content/index');
        }

        // draw view output
        return $this->view->render('content/publish', [
            'records' => $records->get(),
            'model' => $model
        ]);
    }
}
