<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;

/**
 * Class FormRegister. User registration business logic model
 * @package Apps\Model\Front\User
 */
class FormRegister extends Model
{
    public $email;
    public $login;
    public $password;
    public $repassword;
    public $captcha;

    private $_captcha = false;
    /** @var User|null */
    public $_userObject;
    /** @var Profile|null */
    public $_profileObject;

    /**
     * FormRegister constructor. Build model and set maker if captcha is enabled
     * @param bool $captcha
     */
    public function __construct($captcha = false)
    {
        $this->_captcha = $captcha;
        parent::__construct(true);
    }


    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['login', 'password', 'repassword', 'email'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3'],
            ['email', 'email'],
            ['repassword', 'equal', $this->getRequest('password', $this->getSubmitMethod())],
            ['captcha', 'used']
        ];

        if (true === $this->_captcha) {
            $rules[] = ['captcha', 'App::$Captcha::validate'];
        }

        return $rules;
    }

    /**
     * Labels for form items
     * @return array
     */
    public function labels(): array
    {
        return [
            'login' => __('Login'),
            'password' => __('Password'),
            'repassword' => __('Repeat password'),
            'email' => __('Email'),
            'captcha' => __('Captcha')
        ];
    }

    /**
     * Try to insert user data in database
     * @param bool $activation
     * @return bool
     */
    public function tryRegister($activation = false)
    {
        $check = App::$User->where('login', '=', $this->login)
            ->orWhere('email', '=', $this->email)
            ->count();
        if ($check !== 0) {
            return false;
        }

        // create row
        $user = new User();
        $user->login = $this->login;
        $user->email = $this->email;
        $user->password = Crypt::passwordHash($this->password);
        // if need to be approved - make random token and send email
        if ($activation) {
            $user->approve_token = Crypt::randomString(mt_rand(32, 128)); // random token for validation url
            // send email
            if (App::$Mailer) {
                App::$Mailer->tpl('user/_mail/approve', [
                    'token' => $user->approve_token,
                    'email' => $user->email,
                    'login' => $user->login
                ])->send($this->email, (new \Swift_Message(App::$Translate->get('Default', 'Registration approve', []))));
            }
        }
        // save row
        $user->save();

        // create profile
        $profile = new Profile();
        $profile->user_id = $user->id;
        // save profile
        $profile->save();

        // set user & profile objects to attributes to allow extending this model
        $this->_userObject = $user;
        $this->_profileObject = $profile;

        return true;
    }
}
