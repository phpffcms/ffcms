<?php

namespace Apps\Model\Admin;

use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\User;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Interfaces\iUser;
use Ffcms\Core\App;

class UserUpdateForm extends Model
{
    public $email;
    public $login;
    public $nick;
    public $password;
    public $newpassword;
    public $role_id;
    public $is_aproved;

    // constructor link to user identity
    public $_user;

    /**
     * Allow send user object
     * @param iUser $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct();
    }

    /**
    * Load user data on before method
    */
    public function before()
    {
        foreach ($this->getAllProperties() as $property => $old_data) {
            if (null !== $this->_user->$property) {
                $this->$property = $property === 'custom_data' ? unserialize($this->_user->$property) : $this->_user->$property;
            }
        }
    }


    public function labels()
    {
        return [
            'email' => __('Email'),
            'login' => __('Login'),
            'nick' => __('Nickname'),
            'newpassword' => __('New password'),
            'role_id' => __('Role'),
            'is_aproved' => __('Approved')
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validate()
    */
    public function rules()
    {
        return [
            [['email', 'login', 'role_id', 'is_aproved'], 'required'],
            [['nick', 'newpassword', 'custom_data'], 'used'],
            ['email', 'email'],
            ['login', 'length_min', 3],
            ['email', 'Apps\Model\Admin\UserUpdateForm::isUniqueEmail', $this->_user->get('id')],
            ['login', 'Apps\Model\Admin\UserUpdateForm::isUniqueLogin', $this->_user->get('id')]
        ];
    }

    /**
     * Get all roles as id=>name array
     * @return array|null
     */
    public function getRoleList()
    {
        return Role::getIdNameAll();
    }

    /**
     * Update user information
     */
    public function save()
    {
        foreach ($this->getAllProperties() as $property => $value) {
            if ($property === 'password' || $property === 'newpassword') {
                // update password only if new is set and length >= 3
                if ($this->newpassword !== null && String::length($this->newpassword) >= 3) {
                    $this->_user->password = App::$Security->password_hash($this->newpassword);
                }
            } else {
                $this->_user->$property = $value;
            }
        }

        $this->_user->save();
    }

    /**
     * Check if new email is always exist
     * @param string $email
     * @param int $userId
     * @return bool
     */
    public static function isUniqueEmail($email, $userId)
    {
        $find = User::where('email', '=', $email)->where('id', '!=', $userId)->count();

        return $find === 0;
    }

    /**
     * Check if new login is always exist
     * @param string $login
     * @param int $userId
     * @return bool
     */
    public static function isUniqueLogin($login, $userId)
    {
        $find = User::where('login', '=', $login)->where('id', '!=', $userId)->count();

        return $find === 0;
    }
}