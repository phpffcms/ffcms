<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormCategoryDelete;
use Apps\Model\Admin\Content\FormCategoryUpdate;
use Apps\Model\Admin\Content\FormContentClear;
use Apps\Model\Admin\Content\FormContentDelete;
use Apps\Model\Admin\Content\FormContentGlobDelete;
use Apps\Model\Admin\Content\FormContentRestore;
use Apps\Model\Admin\Content\FormContentUpdate;
use Apps\Model\Admin\Content\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Apps\ActiveRecord\Content as ContentEntity;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;

class Content extends AdminController
{
    const VERSION = 0.1;
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
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        $query = null;
        // get query type (trash, category, all)
        $type = App::$Request->query->get('type');
        if ($type === 'trash') {
            $query = ContentEntity::onlyTrashed();
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


        return App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination,
            'type' => $type
        ]);
    }

    /**
     * Edit and add content items
     * @param $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
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
        return App::$View->render('content_update', [
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
            App::$Response->redirect('content/index');
        }

        return App::$View->render('content_delete', [
            'model' => $model->export()
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
            App::$Response->redirect('content/index');
        }

        // draw response
        return App::$View->render('content_restore', [
            'model' => $model->export()
        ]);
    }

    /**
     * Clear the trashed items
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
        return App::$View->render('content_clear', [
            'model' => $model->export()
        ]);
    }

    /**
     * Display category list
     */
    public function actionCategories()
    {
        return App::$View->render('category_list');
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
            App::$Response->redirect('content/categories');
        }

        // draw view
        return App::$View->render('category_delete', [
            'model' => $model->export()
        ]);
    }

    /**
     * Show category edit and create
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
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
        return App::$View->render('category_update', [
            'model' => $model->export()
        ]);
    }

    /**
     * Show content global delete
     * @return string
     * @throws NotFoundException
     * @throws SyntaxException
     */
    public function actionGlobdelete()
    {
        // get content ids from request
        $ids = App::$Request->query->get('selectRemove');

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
            App::$Response->redirect('content/index');
        }

        // return response
        return App::$View->render('content_glob_delete', [
            'model' => $model
        ]);
    }

    /**
     * Show settings form with prepared model
     * @return string
     * @throws SyntaxException
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
                App::$Response->redirect('content/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // draw response
        return App::$View->render('settings', [
            'model' => $model->export()
        ]);
    }
}