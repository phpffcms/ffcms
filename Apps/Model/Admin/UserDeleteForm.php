<?php

namespace Apps\Model\Admin;

use Apps\ActiveRecord\Wall;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Interfaces\iUser;

class UserDeleteForm extends Model
{
    public $email, $login;

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
        $this->email = $this->_user->get('email');
        $this->login = $this->_user->get('login');
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
        Wall::where('target_id', '=', $this->_user->get('id'))
            ->orwhere('sender_id', '=', $this->_user->get('id'))
            ->delete();
        // delete avatars
        File::remove('/upload/user/avatar/big/' . $this->_user->get('id') . '.jpg');
        File::remove('/upload/user/avatar/medium/' . $this->_user->get('id') . '.jpg');
        File::remove('/upload/user/avatar/small/' . $this->_user->get('id') . '.jpg');
        // delete user row
        $this->_user->delete();
    }


}