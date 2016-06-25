<?php

namespace Apps\Model\Front\User;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;
use Apps\ActiveRecord\UserLog;

class FormLogin extends Model
{
    public $login;
    public $password;
    public $captcha;

    public $csrf_token;

    private $_captcha = false;

    /**
     * Construct FormLogin. Pass is captcha used inside
     * @param bool $captcha
     */
    public function __construct($captcha = false)
    {
        $this->_captcha = $captcha;
        parent::__construct();
    }

    /**
     * Login validation rules
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['login', 'password', 'csrf_token'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3'],
            ['captcha', 'used'],
            ['csrf_token', 'csrf_check', 'csrf_token']
        ];
        if (true === $this->_captcha) {
            $rules[] = ['captcha', 'App::$Captcha::validate'];
        }
        return $rules;
    }

    /**
     * Form labels
     * @return array
     */
    public function labels()
    {
        return [
            'login' => __('Login or email'),
            'password' => __('Password'),
            'captcha' => __('Captcha')
        ];
    }

    /**
     * Try user auth after form validate
     * @return bool
     */
    public function tryAuth()
    {
        $password = App::$Security->password_hash($this->password);

        $search = App::$User
            ->where('password', '=', $password)
            ->where(function($query) {
                $query->where('login', '=', $this->login)
                    ->orWhere('email', '=', $this->login);
            });

        if ($search->count() === 1) {
            $object = $search->first();
            // check if accounts is approved
            if ($object->approve_token !== '0' && Str::length($object->approve_token) > 0) {
                return false;
            }
            return $this->openSession($object);
        }

        return false;
    }

    /**
     * Open session and store data token to db
     * @param iUser $userObject
     * @return bool
     */
    public function openSession(iUser $userObject)
    {
        if ($userObject === null || $userObject->id < 1) {
            return false;
        }

        // write session data
        App::$Session->set('ff_user_id', $userObject->id);

        // write user log
        $log = new UserLog();
        $log->user_id = $userObject->id;
        $log->type = 'AUTH';
        $log->message = __('Successful authorization from ip: %ip%', ['ip' => App::$Request->getClientIp()]);
        $log->save();

        return true;
    }
}