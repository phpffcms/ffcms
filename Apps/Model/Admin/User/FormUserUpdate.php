<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\User;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormUserUpdate. Update user data business logic model
 * @package Apps\Model\Admin\User
 */
class FormUserUpdate extends Model
{
    public $email;
    public $login;
    public $password;
    public $newpassword;
    public $role_id;
    public $approved = true;

    public $approve_token;

    /** @var iUser */
    public $_user;

    /**
     * FormUserUpdate constructor. Pass user object inside the model
     * @param iUser $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct(true);
    }

    /**
     * Load user data on before method
     */
    public function before()
    {
        foreach ($this->getAllProperties() as $property => $old_data) {
            if ($this->_user->{$property}) {
                $this->{$property} = $this->_user->{$property};
            }
        }

        if (!$this->approve_token) {
            $this->approved = true;
        }
    }

    /**
     * Form labels to display
     * @return array
     */
    public function labels(): array
    {
        return [
            'email' => __('Email'),
            'login' => __('Login'),
            'newpassword' => __('New password'),
            'role_id' => __('Role'),
            'approved' => __('Approved')
        ];
    }

    /**
     * Validation rules for input data
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'login', 'role_id', 'approved'], 'required'],
            ['newpassword', 'used'],
            ['email', 'email'],
            ['login', 'length_min', 3],
            ['email', 'Apps\Model\Admin\User\FormUserUpdate::isUniqueEmail', $this->_user->getParam('id')],
            ['login', 'Apps\Model\Admin\User\FormUserUpdate::isUniqueLogin', $this->_user->getParam('id')]
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
     * Update user information in database based on current obj attributes passed from input data
     */
    public function save()
    {
        foreach ($this->getAllProperties() as $property => $value) {
            if ($property === 'password' || $property === 'newpassword') {
                // update password only if new is set and length >= 3
                if ($this->newpassword && Str::length($this->newpassword) >= 3) {
                    $this->_user->password = Crypt::passwordHash($this->newpassword);
                }
            } elseif ($property === 'approved') {
                if ($this->approved) {
                    $this->_user->approve_token = null;
                } else {
                    $this->_user->approve_token = $this->approve_token ?? Crypt::randomString(mt_rand(32, 128));
                }
            } elseif ($property === 'approve_token') {
                continue;
            } else {
                $this->_user->{$property} = $value;
            }
        }

        // get user id before save to determine "add" action
        $id = $this->_user->id;
        // safe user row
        $this->_user->save();
        // if new user - add profile link
        if ($id < 1) {
            $profile = new Profile();
            $profile->user_id = $this->_user->id;
            $profile->save();
        }
    }

    /**
     * Check if new email is always exist
     * @param string $email
     * @param int|null $userId
     * @return bool
     */
    public static function isUniqueEmail($email, $userId = null)
    {
        $find = User::where('email', '=', $email);

        if ($userId && Any::isInt($userId)) {
            $find->where('id', '!=', $userId);
        }

        return $find->count() === 0;
    }

    /**
     * Check if new login is always exist
     * @param string $login
     * @param int|null $userId
     * @return bool
     */
    public static function isUniqueLogin($login, $userId = null)
    {
        $find = User::where('login', '=', $login);

        if ($userId && Any::isInt($userId)) {
            $find->where('id', '!=', $userId);
        }

        return $find->count() === 0;
    }
}
