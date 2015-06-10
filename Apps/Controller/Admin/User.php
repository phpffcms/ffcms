<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Role;
use Apps\Model\Admin\UserDeleteForm;
use Apps\Model\Admin\UserUpdateForm;
use Extend\Core\Arch\AdminController;
use Apps\Model\Basic\User as UserRecords;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;


class User extends AdminController
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
        if ($id < 1 || !App::$User->isExist($id)) {
            throw new NotFoundException('User is not founded');
        }

        // find user identify object
        $user = App::$User->identity($id);
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
        $roles = Role::getAll();

        $this->response = App::$View->render('group_list', [
            'records' => $roles
        ]);
    }

    public function actionGroupEdit($id)
    {
        $this->response = 'Test';
    }
}