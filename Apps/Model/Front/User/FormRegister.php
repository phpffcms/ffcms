<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Apps\Model\Front\User\FormLogin;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\String;

class FormRegister extends Model
{
    public $email;
    public $login;
    public $password;
    public $repassword;
    public $captcha;

    private $_captcha = false;

    /**
     * Build model and set maker if captcha is enabled
     * @param bool $captcha
     */
    public function __construct($captcha = false)
    {
        parent::__construct();
        $this->_captcha = $captcha;
    }


    /**
     * Validation rules
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['login', 'password', 'repassword', 'email'], 'required'],
            ['login', 'length_min', '2'],
            ['password', 'length_min', '3'],
            ['email', 'email'],
            ['repassword', 'equal', $this->getInput('password')],
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
    public function labels()
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

        $password = App::$Security->password_hash($this->password);
        // create row
        $user = new User();
        $user->login = $this->login;
        $user->email = $this->email;
        $user->password = $password;
        // if need to be approved - make random token and send email
        if (true === $activation) {
            $user->approve_token = String::randomLatinNumeric(mt_rand(32, 128)); // random token for validation url
            // send email
            $template = App::$View->render('user/_approveMail', [
                'token' => $user->approve_token,
                'email' => $user->email,
                'login' => $user->login
            ]);

            $sender = App::$Property->get('adminEmail');

            // format SWIFTMailer format
            $mailMessage = \Swift_Message::newInstance(App::$Translate->get('Default', 'Registration approve', []))
                ->setFrom([$sender])
                ->setTo([$this->email])
                ->setBody($template);
            // send message
            App::$Mailer->send($mailMessage);
        }
        // save row
        $user->save();
        // create profile
        $profile = new Profile();
        $profile->user_id = $user->id;
        // save profile
        $profile->save();

        // just make auth and redirect ;)
        if (false === $activation) {
            $loginModel = new FormLogin();
            $loginModel->openSession($user);
            App::$Response->redirect('/'); // session is opened, refresh page
        }

        return true;
    }
}