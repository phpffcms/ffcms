<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Content as ContentEntity;
use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormCategoryDelete;
use Apps\Model\Admin\Content\FormCategoryUpdate;
use Apps\Model\Admin\Content\FormContentClear;
use Apps\Model\Admin\Content\FormContentDelete;
use Apps\Model\Admin\Content\FormContentGlobDelete;
use Apps\Model\Admin\Content\FormContentPublish;
use Apps\Model\Admin\Content\FormContentRestore;
use Apps\Model\Admin\Content\FormContentUpdate;
use Apps\Model\Admin\Content\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Obj;

/**
 * Class Content. Admin controller to manage & control contents
 * @package Apps\Controller\Admin
 */
class Content extends AdminController
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    /**
     * List content items
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionIndex()
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        $query = null;
        // get query type (trash, category, all)
        $type = $this->request->query->get('type');
        if ($type === 'trash') {
            $query = ContentEntity::onlyTrashed();
        } elseif ($type === 'moderate') { // only items on moderate
            $query = ContentEntity::where('display', '=', 0);
        } elseif (Obj::isLikeInt($type)) { // sounds like category id ;)
            $query = ContentEntity::where('category_id', '=', (int)$type);
        } else {
            $query = new ContentEntity();
            $type = 'all';
        }

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['content/index', null, null, ['type' => $type]],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination,
            'type' => $type
        ]);
    }

    /**
     * Edit and add content items
     * @param int|null $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionUpdate($id = null)
    {
        // get item with trashed objects
        $record = ContentEntity::withTrashed()->findOrNew($id);
        $isNew = $record->id === null;

        // init model
        $model = new FormContentUpdate($record);

        // check if model is submit
        if ($model->send() && $model->validate()) {
            $model->save();
            if ($isNew === true) {
                $this->response->redirect('content/index');
            }
            App::$Session->getFlashBag()->add('success', __('Content is successful updated'));
        }

        // draw response
        return $this->view->render('content_update', [
            'model' => $model
        ]);
    }

    /**
     * Delete content by id
     * @param int $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionDelete($id)
    {
        if (!Obj::isLikeInt($id) || $id < 1) {
            throw new NotFoundException();
        }

        // get content record and check availability
        $record = ContentEntity::find($id);
        if ($record === null || $record === false) {
            throw new NotFoundException();
        }

        // init delete model
        $model = new FormContentDelete($record);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Content is successful moved to trash'));
            $this->response->redirect('content/index');
        }

        return $this->view->render('content_delete', [
            'model' => $model
        ]);
    }

    /**
     * Restore deleted content
     * @param $id
     * @return string
     * @throws NotFoundException
     * @throws SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionRestore($id)
    {
        if (!Obj::isLikeInt($id) || $id < 1) {
            throw new NotFoundException();
        }

        // get removed object
        $record = ContentEntity::onlyTrashed()->find($id);
        if ($record === null || $record === false) {
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
        return $this->view->render('content_restore', [
            'model' => $model
        ]);
    }

    /**
     * Clear all trashed items
     * @return string
     * @throws SyntaxException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionClear()
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

    /**
     * Display category list
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionCategories()
    {
        return $this->view->render('category_list', [
            'categories' => ContentCategory::getSortedAll()
        ]);
    }

    /**
     * Delete category action
     * @param int $id
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionCategorydelete($id)
    {
        // check id
        if (!Obj::isLikeInt($id) || $id < 2) {
            throw new ForbiddenException();
        }

        // get object relation
        $record = ContentCategory::find($id);
        if ($record === null || $record === false) {
            throw new ForbiddenException();
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
        return $this->view->render('category_delete', [
            'model' => $model
        ]);
    }

    /**
     * Show category edit and create
     * @param int|null $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionCategoryupdate($id = null)
    {
        // get owner id for new rows
        $parentId = (int)$this->request->query->get('parent');

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
        return $this->view->render('category_update', [
            'model' => $model
        ]);
    }

    /**
     * Show content global delete
     * @return string
     * @throws NotFoundException
     * @throws SyntaxException
     * @throws NativeException
     */
    public function actionGlobdelete()
    {
        // get content ids from request
        $ids = $this->request->query->get('selected');

        // check if input is array
        if (!Obj::isArray($ids) || count($ids) < 1) {
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
        return $this->view->render('content_glob_delete', [
            'model' => $model
        ]);
    }

    /**
     * Publish content on moderate stage
     * @return string
     * @throws NotFoundException
     * @throws SyntaxException
     * @throws NativeException
     */
    public function actionPublish()
    {
        // get ids as array from GET
        $ids = $this->request->query->get('selected');
        if (!Obj::isArray($ids) || count($ids) < 1) {
            throw new NotFoundException(__('Items to publish is not found'));
        }

        // try to find items in db
        $records = ContentEntity::whereIn('id', $ids)->where('display', '=', 0);
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
        return $this->view->render('publish', [
            'records' => $records->get(),
            'model' => $model
        ]);
    }

    /**
     * Show settings form with prepared model
     * @return string
     * @throws SyntaxException
     * @throws NativeException
     */
    public function actionSettings()
    {
        // init model with config array data
        $model = new FormSettings($this->getConfigs());

        // check if form is send
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('content/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // draw response
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }
}