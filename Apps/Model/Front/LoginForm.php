<?php

namespace Apps\Model\Front;

use Ffcms\Core\Arch\Model;

class LoginForm extends Model
{

    public $login;
    public $password;
    public $captcha;

    public function setRules()
    {
        return [
            [['login', 'password'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3']
        ];
    }

    public function setLabels()
    {
        return [
            'login' => __('Login'),
            'password' => __('Password')
        ];
    }

    public function checkData()
    {

        return false;
    }
}