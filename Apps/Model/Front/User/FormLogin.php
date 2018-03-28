<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\User;
use Apps\ActiveRecord\UserLog;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormLogin. User login business logic model
 * @package Apps\Model\Front\User
 */
class FormLogin extends Model
{
    public $login;
    public $password;
    public $captcha;

    private $_captcha = false;

    /**
     * Construct FormLogin. Pass is captcha used inside
     * @param bool $captcha
     */
    public function __construct($captcha = false)
    {
        $this->_captcha = $captcha;
        // tell that we shall use csrf protection
        parent::__construct(true);
    }

    /**
     * Login validation rules
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['login', 'password'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3'],
            ['captcha', 'used']
        ];
        if ($this->_captcha) {
            $rules[] = ['captcha', 'App::$Captcha::validate'];
        }
        return $rules;
    }

    /**
     * Form labels
     * @return array
     */
    public function labels(): array
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
    public function tryAuth(): bool
    {
        $password = App::$Security->password_hash($this->password);

        $search = App::$User->where('password', '=', $password)->where(function ($query) {
            $query->where('login', '=', $this->login)
                ->orWhere('email', '=', $this->login);
        });

        if ($search->count() === 1) {
            /** @var User $object */
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
    public function openSession(iUser $userObject): bool
    {
        if (!$userObject || $userObject->id < 1) {
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
