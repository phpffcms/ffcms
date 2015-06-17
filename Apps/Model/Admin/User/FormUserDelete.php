<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Interfaces\iUser;

class FormUserDelete extends Model
{
    public $email;
    public $login;

    private $_user;

    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct();
    }

    /**
    * Set user data to model property
    */
    public function before()
    {
        $this->email = $this->_user->getParam('email');
        $this->login = $this->_user->getParam('login');
    }

    /**
     * Default labels
     * @return array
     */
    public function labels()
    {
        return [
            'email' => __('Email'),
            'login' => __('Login')
        ];
    }

    /**
     * Delete user from database
     */
    public function delete()
    {
        // delete wall records
        WallPost::where('target_id', '=', $this->_user->getParam('id'))
            ->orwhere('sender_id', '=', $this->_user->getParam('id'))
            ->delete();
        // delete avatars
        File::remove('/upload/user/avatar/big/' . $this->_user->getParam('id') . '.jpg');
        File::remove('/upload/user/avatar/medium/' . $this->_user->getParam('id') . '.jpg');
        File::remove('/upload/user/avatar/small/' . $this->_user->getParam('id') . '.jpg');
        // delete user profile and auth data
        $this->_user->getProfile()->delete();
        $this->_user->delete();
    }


}