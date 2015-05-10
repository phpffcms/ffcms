<?php

namespace Apps\Model\Front;

use Extend\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;

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

    /**
     * Try user auth after form validate
     * @return bool
     */
    public function tryAuth()
    {
        $password = App::$Security->password_hash($this->password);
        $search = App::$User
            ->where('password', '=', $password)
            ->where(function($query){
                $query->where('login', '=', $this->login)
                    ->orWhere('email', '=', $this->login);
            });


        if ($search->count() === 1) {
            $uData = $search->first();
            return $this->openSession($uData->id);
        }

        return false;
    }

    /**
     * Open session and store data token to db
     * @param int $userId
     * @return bool
     */
    public function openSession($userId)
    {
        if (!Object::isInt($userId) || $userId < 1) {
            return false;
        }

        $token = String::randomLatin(rand(128, 255));
        $user = App::$User->find($userId);

        if ($user->count() !== 1) {
            return false;
        }

        // write session data
        App::$Session->start();
        App::$Session->set('ff_user_id', $userId);
        App::$Session->set('ff_user_token', $token);

        // write token to db
        $user->token_data = $token;
        $user->save();

        return true;
    }
}