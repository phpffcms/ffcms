<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\ProfileField;
use Apps\Model\Admin\Profile\FormFieldUpdate;
use Apps\Model\Admin\Profile\FormSettings;
use Apps\Model\Front\Profile\FormSettings as FrontFormSettings;
use Extend\Core\Arch\AdminController;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Obj;

class Profile extends AdminController
{
    const VERSION = 0.1;

    const ITEM_PER_PAGE = 10;

    /**
     * List all profiles in website with pagination
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // init Active Record
        $query = new ProfileRecords();

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // display viewer
        return App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * Redirect to user controller
     * @param $id
     */
    public function actionDelete($id)
    {
        App::$Response->redirect('user/delete/' . $id);
    }

    /**
     * Profile edit action
     * @param int $id
     * @return string
     * @throws NotFoundException
     */
    public function actionUpdate($id)
    {
        if (!Obj::isLikeInt($id) || $id < 1) {
            throw new NotFoundException();
        }

        // get user profile via id
        $profile = ProfileRecords::find($id);
        if (false === $profile || null === $profile) {
            throw new NotFoundException();
        }

        // check if user id for this profile_id is exist
        if (!App::$User->isExist($profile->user_id)) {
            throw new NotFoundException();
        }

        // get user object from profile
        $user = $profile->User();
        $model = new FrontFormSettings($user);

        if ($model->send() && $model->validate()) {
            $model->save();
            App::$Session->getFlashBag()->add('success', __('Profile is updated'));
        }

        return App::$View->render('update', [
            'model' => $model->export(),
            'user' => $user,
            'profile' => $profile
        ]);
    }

    /**
     * List additional fields
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionFieldlist()
    {
        $records = ProfileField::all();

        return App::$View->render('field_list', [
            'records' => $records
        ]);
    }

    /**
     * Add and edit additional fields data
     * @param $id
     * @return string
     */
    public function actionFieldupdate($id)
    {
        // get current record or new and init form DI
        $record = ProfileField::findOrNew($id);
        $model = new FormFieldUpdate($record);

        $isNew = false;
        if ($record->id === null) {
            $isNew = true;
        }
        // check if form is submited
        if ($model->send() && $model->validate()) {
            $model->save();
            if (true === $isNew) {
                App::$Response->redirect('profile/fieldlist');
            }
            App::$Session->getFlashBag()->add('success', __('Profile field was successful updated'));
        }

        return App::$View->render('field_update', [
            'model' => $model->export()
        ]);
    }

    /**
     * Delete custom field action
     * @param int $id
     * @return string
     * @throws ForbiddenException
     */
    public function actionFielddelete($id)
    {
        if (!Obj::isLikeInt($id) || $id < 1) {
            throw new ForbiddenException();
        }

        // check if record with $id is exist
        $record = ProfileField::find($id);
        if ($record === null || $record === false) {
            throw new ForbiddenException();
        }

        $model = new FormFieldUpdate($record);

        // if delete is submited - lets remove this record
        if ($model->send()) {
            $model->delete();
            App::$Response->redirect('profile/fieldlist');
        }

        return App::$View->render('field_delete', [
            'model' => $model->export()
        ]);
    }

    /**
     * Show profiles settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings()
    {
        $model = new FormSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Response->redirect('profile/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        return App::$View->render('settings', [
            'model' => $model
        ]);
    }


}