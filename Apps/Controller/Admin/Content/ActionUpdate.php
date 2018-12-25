<?php

namespace Apps\Controller\Admin\Content;

use Apps\Model\Admin\Content\FormContentUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionUpdate
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionUpdate
{
    /**
     * Edit and add content items
     * @param string|null $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function update(?string $id = null): ?string
    {
        // get item with trashed objects
        $record = ContentEntity::withTrashed()
            ->findOrNew($id);
        $isNew = $record->id === null;
        $cloneId = (int)$this->request->query->get('from', 0);

        // init model
        $model = new FormContentUpdate($record, $cloneId);

        // check if model is submit
        if ($model->send() && $model->validate()) {
            $model->save();
            if ($isNew) {
                $this->response->redirect('content/index');
            }
            App::$Session->getFlashBag()->add('success', __('Content is successful updated'));
        }

        // draw response
        return $this->view->render('content/content_update', [
            'model' => $model
        ]);
    }
}
