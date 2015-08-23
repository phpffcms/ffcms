<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormCategoryDelete;
use Apps\Model\Admin\Content\FormCategoryUpdate;
use Apps\Model\Admin\Content\FormContentClear;
use Apps\Model\Admin\Content\FormContentDelete;
use Apps\Model\Admin\Content\FormContentRestore;
use Apps\Model\Admin\Content\FormContentUpdate;
use Apps\Model\Admin\Content\FormSettings;
use Extend\Core\Arch\AdminAppController;
use Ffcms\Core\App;
use Apps\ActiveRecord\Content as ContentEntity;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Object;

class Content extends AdminAppController
{
    const ITEM_PER_PAGE = 10;

    /**
     * List content items
     */
    public function actionIndex()
    {
        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        $query = null;
        // get query type (trash, category, all)
        $type = App::$Request->query->get('type');
        if ($type === 'trash') {
            $query = ContentEntity::onlyTrashed();
        } elseif (Object::isLikeInt($type)) { // sounds like category id ;)
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


        $this->response = App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination,
            'type' => $type
        ]);
    }

    /**
     * Edit and add content items
     * @param $id
     */
    public function actionUpdate($id)
    {
        // get item with trashed objects
        $record = ContentEntity::withTrashed()->find($id);
        $isNew = $record->id === null;

        // create empty object if its new
        if ($isNew === true) {
            $record = new ContentEntity();
        }

        // init model
        $model = new FormContentUpdate($record);

        // check if model is submit
        if ($model->send() && $model->validate()) {
            $model->save();
            if ($isNew === true) {
                App::$Response->redirect('content/index');
            }
            App::$Session->getFlashBag()->add('success', __('Content is successful updated'));
        }

        // draw response
        $this->response = App::$View->render('content_update', [
            'model' => $model
        ]);
    }

    /**
     * Delete content by id
     * @param int $id
     * @throws NotFoundException
     */
    public function actionDelete($id)
    {
        if (!Object::isLikeInt($id) || $id < 1) {
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
            App::$Response->redirect('content/index');
        }

        $this->response = App::$View->render('content_delete', [
            'model' => $model->export()
        ]);
    }

    /**
     * Restore deleted content
     * @param $id
     * @throws NotFoundException
     */
    public function actionRestore($id)
    {
        if (!Object::isLikeInt($id) || $id < 1) {
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
            App::$Response->redirect('content/index');
        }

        // draw response
        $this->response = App::$View->render('content_restore', [
            'model' => $model->export()
        ]);
    }

    public function actionClear()
    {
        // find trashed rows
        $records = ContentEntity::onlyTrashed();

        // init model
        $model = new FormContentClear($records->count());
        if ($model->send() && $model->validate()) {
            // remove all trashed items
            foreach ($records->get() as $item) {
                $galleryPath = '/upload/gallery/' . (int)$item->id;
                if (Directory::exist($galleryPath)) {
                    Directory::remove($galleryPath);
                }
            }
            // totally remove rows from db
            $records->forceDelete();
            App::$Session->getFlashBag()->add('success', __('Trashed content is cleanup'));
            App::$Response->redirect('content/index');
        }

        // draw response
        $this->response = App::$View->render('content_clear', [
            'model' => $model->export()
        ]);
    }

    /**
     * Display category list
     */
    public function actionCategories()
    {
        $this->response = App::$View->render('category_list');
    }

    /**
     * Delete category action
     * @param int $id
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionCategorydelete($id)
    {
        // check id
        if (!Object::isLikeInt($id) || $id < 2) {
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
            App::$Response->redirect('content/categories');
        }

        // draw view
        $this->response = App::$View->render('category_delete', [
            'model' => $model->export()
        ]);
    }

    /**
     * Show category edit and create
     * @param null $id
     */
    public function actionCategoryupdate($id = null)
    {
        // get owner id for new rows
        $parentId = (int)App::$Request->query->get('parent');

        // get relation and pass to model
        $record = ContentCategory::findOrNew($id);
        $isNew = $record->id === null;
        $model = new FormCategoryUpdate($record, $parentId);

        // if model is submited
        if ($model->send() && $model->validate()) {
            $model->save();
            // if is new - redirect to list after submit
            if ($isNew) {
                App::$Response->redirect('content/categories');
            }
            // show notify message
            App::$Session->getFlashBag()->add('success', __('Category is successful updated'));
        }

        // draw response view and pass model properties
        $this->response = App::$View->render('category_update', [
            'model' => $model->export()
        ]);
    }

    /**
     * Content app settings
     */
    public function actionSettings()
    {
        // init model with config array data
        $model = new FormSettings($this->getConfigs());

        // check if form is send
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Response->redirect('content/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // draw response
        $this->response = App::$View->render('settings', [
            'model' => $model->export()
        ]);
    }
}