<?php

namespace Apps\Model\Front;

use Extend\Core\App;
use Ffcms\Core\Arch\Model;

class RegisterForm extends Model
{

    public $email;
    public $login;
    public $password;
    public $repassword;


    public function setRules()
    {
        return [
            [['login', 'password', 'repassword', 'email'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3'],
            ['email', 'email'],
            ['repassword', 'equal', App::$Request->get('password')]
        ];
    }

    public function setLabels()
    {
        return [
            'login' => __('Login'),
            'password' => __('Password'),
            'repassword' => __('Repeat password'),
            'email' => __('Email')
        ];
    }

    // after validation
    public function tryRegister()
    {
        $check = App::$User->where('login', '=', $this->login)
            ->orWhere('email', '=', $this->email)
            ->count();
        if ($check !== 0) {
            return false;
        }

        $password = App::$Security->password_hash($this->password);
        // create row
        $user = App::$User;
        $user->login = $this->login;
        $user->email = $this->email;
        $user->password = $password;
        $user->save();

        $loginModel = new LoginForm();
        $loginModel->openSession($user->id);

        return true;
    }
}