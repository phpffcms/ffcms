<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Role;
use Apps\Model\Admin\SendInviteForm;
use Apps\Model\Admin\UserDeleteForm;
use Apps\Model\Admin\UserGroupUpdateForm;
use Apps\Model\Admin\UserSettings;
use Apps\Model\Admin\UserUpdateForm;
use Extend\Core\Arch\AdminAppController;
use Apps\Model\Basic\User as UserRecords;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;


class User extends AdminAppController
{
    const ITEM_PER_PAGE = 10;

    // list users
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
        $this->response = App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    // edit user profiles
    public function actionUpdate($id)
    {
        $user = \Apps\Model\Basic\User::findOrNew($id);

        // find user identify object
        //$user = App::$User->identity($id);
        // generate model data based on user object
        $model = new UserUpdateForm($user);

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
        $this->response = App::$View->render('user_update', [
            'model' => $model->export()
        ]);
    }

    /**
     * Delete user data
     * @param $id
     * @throws NotFoundException
     */
    public function actionDelete($id)
    {
        if ($id < !1 || !App::$User->isExist($id)) {
            throw new NotFoundException('User is not founded');
        }

        // get user object and load model
        $user = App::$User->identity($id);
        $model = new UserDeleteForm($user);

        if ($model->send()) {
            $model->delete();
            App::$Response->redirect('user/index');
        } else {
            App::$Session->getFlashBag()->add('error', __('There is no way to revert this action! Be careful!'));
        }

        // set view response
        $this->response = App::$View->render('user_delete', [
            'model' => $model
        ]);
    }

    /**
     * Show all role groups
     */
    public function actionGrouplist()
    {
        // get all roles
        $roles = Role::getAll();

        $this->response = App::$View->render('group_list', [
            'records' => $roles
        ]);
    }

    /**
     * Edit and add groups
     * @param $id
     */
    public function actionGroupUpdate($id)
    {
        // find role or create new object
        $role = Role::findOrNew($id);

        $model = new UserGroupUpdateForm($role);
        if ($model->send()) { // work with post request
            if ($model->validate()) {
                $model->save();
                App::$Session->getFlashBag()->add('success', __('Data was successful updated'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        $this->response = App::$View->render('group_update', [
            'model' => $model
        ]);
    }

    /**
     * User identity settings
     */
    public function actionSettings()
    {
        // load model and pass property's as argument
        $model = new UserSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Response->redirect('user/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        $this->response = App::$View->render('settings', [
            'model' => $model->export()
        ]);
    }

    /**
     * Send invite to users
     */
    public function actionInvite()
    {
        // init model
        $model = new SendInviteForm();

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
        $this->response = App::$View->render('invite', [
            'model' => $model
        ]);
    }
}