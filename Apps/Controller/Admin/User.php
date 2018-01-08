<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\User as UserRecords;
use Apps\Model\Admin\User\FormInviteSend;
use Apps\Model\Admin\User\FormUserDelete;
use Apps\Model\Admin\User\FormUserGroupUpdate;
use Apps\Model\Admin\User\FormUserSettings;
use Apps\Model\Admin\User\FormUserUpdate;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;

/**
 * Class User. Admin controller of user application.
 * @package Apps\Controller\Admin
 */
class User extends AdminController
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    /**
     * List all users as table
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // init Active Record
        $query = new UserRecords();

        // set current page and offset
        $page = (int)$this->request->query->get('page', 0);
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
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * Edit user profile by id
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionUpdate($id)
    {
        $user = UserRecords::findOrNew($id);
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
        return $this->view->render('user_update', [
            'model' => $model
        ]);
    }

    /**
     * Delete user row from database
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws NotFoundException
     */
    public function actionDelete($id = null)
    {
        // check if id is passed or get data from GET as array ids
        if ((int)$id < 1) {
            $ids = $this->request->query->get('selected');
            if (!Any::isArray($ids) || !Arr::onlyNumericValues($ids)) {
                throw new NotFoundException('Bad conditions');
            }
            $id = $ids;
        } else {
            $id = [$id];
        }

        // initialize delete model
        $model = new FormUserDelete($id);

        // check if users is found
        if ($model->users === null) {
            throw new NotFoundException(__('Users are not found'));
        }

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->delete();
            App::$Session->getFlashBag()->add('success', __('Users and them data are successful removed'));
            $this->response->redirect('user/index');
        }

        // set view response
        return $this->view->render('user_delete', [
            'model' => $model
        ]);
    }

    /**
     * Show all role groups
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionGrouplist()
    {
        // get all roles
        $roles = Role::all();

        return $this->view->render('group_list', [
            'records' => $roles
        ]);
    }

    /**
     * Edit and add groups
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
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
        return $this->view->render('group_update', [
            'model' => $model
        ]);
    }

    /**
     * User identity settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionSettings()
    {
        // load model and pass property's as argument
        $model = new FormUserSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('user/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }

    /**
     * Send invite to user by email
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
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
        return $this->view->render('invite', [
            'model' => $model
        ]);
    }
}
