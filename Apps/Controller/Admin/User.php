<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Role;
use Apps\Model\Admin\User\FormInviteSend;
use Apps\Model\Admin\User\FormUserDelete;
use Apps\Model\Admin\User\FormUserGroupUpdate;
use Apps\Model\Admin\User\FormUserSettings;
use Apps\Model\Admin\User\FormUserUpdate;
use Extend\Core\Arch\AdminController;
use Apps\ActiveRecord\User as UserRecords;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;


class User extends AdminController
{
    const VERSION = 0.1;

    const ITEM_PER_PAGE = 10;

    /**
     * List all users as table
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // init Active Record
        $query = new UserRecords();

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['user/index'],
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
     * Edit user profile by id
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionUpdate($id)
    {
        $user = UserRecords::findOrNew($id);

        // find user identify object
        //$user = App::$User->identity($id);
        // generate model data based on user object
        $model = new FormUserUpdate($user);

        // check is form is sended
        if ($model->send()) {
            if ($model->validate()) { // check validation
                $model->save();
                App::$Session->getFlashBag()->add('success', __('Data was successful updated'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render viewer
        return App::$View->render('user_update', [
            'model' => $model->export()
        ]);
    }

    /**
     * Delete user row from database
     * @param int $id
     * @return string
     * @throws NotFoundException
     */
    public function actionDelete($id)
    {
        if ($id < !1 || !App::$User->isExist($id)) {
            throw new NotFoundException('User is not founded');
        }

        // get user object and load model
        $user = App::$User->identity($id);
        $model = new FormUserDelete($user);

        if ($model->send()) {
            $model->delete();
            App::$Response->redirect('user/index');
        } else {
            App::$Session->getFlashBag()->add('error', __('There is no way to revert this action! Be careful!'));
        }

        // set view response
        return App::$View->render('user_delete', [
            'model' => $model
        ]);
    }

    /**
     * Show all role groups
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionGrouplist()
    {
        // get all roles
        $roles = Role::getAll();

        return App::$View->render('group_list', [
            'records' => $roles
        ]);
    }

    /**
     * Edit and add groups
     * @param int $id
     * @return string
     */
    public function actionGroupUpdate($id)
    {
        // find role or create new object
        $role = Role::findOrNew($id);

        $model = new FormUserGroupUpdate($role);
        if ($model->send()) { // work with post request
            if ($model->validate()) {
                $model->save();
                App::$Session->getFlashBag()->add('success', __('Data was successful updated'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return App::$View->render('group_update', [
            'model' => $model
        ]);
    }

    /**
     * User identity settings
     * @return string
     */
    public function actionSettings()
    {
        // load model and pass property's as argument
        $model = new FormUserSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Response->redirect('user/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return App::$View->render('settings', [
            'model' => $model->export()
        ]);
    }

    /**
     * Send invite to user by email
     * @return string
     */
    public function actionInvite()
    {
        // init model
        $model = new FormInviteSend();

        if ($model->send()) {
            if ($model->validate()) {
                if ($model->make()) {
                    App::$Session->getFlashBag()->add('success', __('Invite was successful send!'));
                } else {
                    App::$Session->getFlashBag()->add('error', __('Mail server connection is failed!'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return App::$View->render('invite', [
            'model' => $model
        ]);
    }
}