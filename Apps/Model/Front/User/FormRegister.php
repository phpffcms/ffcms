<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FormRegister. User registration business logic model
 * @package Apps\Model\Front\User
 */
class FormRegister extends Model
{
    public $email;
    public $password;
    public $repassword;
    public $captcha;

    private $_captcha = false;
    /** @var User|null */
    public $_userObject;
    /** @var Profile|null */
    public $_profileObject;
    /** @var array|null */
    private $_configs;

    /**
     * FormRegister constructor. Build model and set maker if captcha is enabled
     * @param array|null $configs
     */
    public function __construct(?array $configs)
    {
        $this->_configs = $configs;
        $this->_captcha = ($configs['captchaOnRegister'] === 1);
        parent::__construct(true);
    }


    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['password', 'repassword', 'email'], 'required'],
            ['password', 'length_min', '8'],
            ['password', 'passwordStrong'],
            ['email', 'email'],
            ['email', '\Apps\Model\Front\User\FormRegister::isAllowedZone', $this->_configs['allowedEmails']],
            ['repassword', 'equal', $this->getRequest('password', $this->getSubmitMethod())],
            ['captcha', 'used']
        ];

        if ($this->_captcha) {
            $rules[] = ['captcha', 'App::$Captcha::validate'];
        }

        return $rules;
    }

    /**
     * Validate is email from allowedZone if features is enabled
     * @param string $value
     * @return bool
     */
    public static function isAllowedZone($value = null, $allowed = null): bool
    {
        $valid = false;
        if (Str::contains(',', $allowed)) {
            $sub = explode(',', $allowed);
            foreach ($sub as $zone) {
                $zone = trim($zone);
                if (Str::endsWith($zone, $value)) {
                    $valid = true;
                }
            }
        } else {
            $valid = Str::endsWith($allowed, $value);
        }
        
        return $valid;
    }

    /**
     * Labels for form items
     * @return array
     */
    public function labels(): array
    {
        return [
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
    public function tryRegister($activation = false): bool
    {
        $check = App::$User->where('email', $this->email)
            ->count();
        if ($check !== 0) {
            return false;
        }

        // create row
        $user = new User();
        $user->email = $this->email;
        $user->password = Crypt::passwordHash($this->password);
        // if need to be approved - make random token and send email
        if ($activation) {
            $user->approve_token = Crypt::randomString(mt_rand(32, 128)); // random token for validation url
            // send email
            if (App::$Mailer->isEnabled()) {
                App::$Mailer->tpl('user/_mail/approve', [
                    'token' => $user->approve_token,
                    'email' => $user->email
                ])->send($this->email, App::$Translate->get('Default', 'Registration approve', []));
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
