<?php

namespace Apps\Controller\Admin\Content;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormCategoryUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionCategoryUpdate
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionCategoryUpdate
{
    /**
     * Show category edit and create
     * @param string|null $id
     * @return string
     * @throws SyntaxException
     */
    public function categoryUpdate(?string $id = null): ?string
    {
        // get owner id for new rows
        $parentId = $this->request->query->get('parent', null);

        // get relation and pass to model
        $record = ContentCategory::findOrNew($id);
        $isNew = $record->id === null;
        $model = new FormCategoryUpdate($record, $parentId);

        // if model is submited
        if ($model->send() && $model->validate()) {
            $model->save();
            // if is new - redirect to list after submit
            if ($isNew) {
                $this->response->redirect('content/categories');
            }
            // show notify message
            App::$Session->getFlashBag()->add('success', __('Category is successful updated'));
        }

        // draw response view and pass model properties
        return $this->view->render('content/category_update', [
            'model' => $model
        ]);
    }
}
