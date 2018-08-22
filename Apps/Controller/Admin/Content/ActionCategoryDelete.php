<?php

namespace Apps\Controller\Admin\Content;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormCategoryDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionCategoryDelete
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionCategoryDelete
{
    /**
     * Delete category action
     * @param string $id
     * @return string
     * @throws \Exception
     */
    public function categoryDelete(string $id): ?string
    {
        // check id, (1 is the general root category)
        if (!Any::isInt($id) || $id < 2) {
            throw new ForbiddenException();
        }

        // get object relation
        $record = ContentCategory::find($id);
        if (!$record) {
            throw new ForbiddenException(__('Category is not exist'));
        }

        // init model with object relation
        $model = new FormCategoryDelete($record);

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Category is successful removed'));
            $this->response->redirect('content/categories');
        }

        // draw view
        return $this->view->render('content/category_delete', [
            'model' => $model
        ]);
    }
}
