<?php

namespace Apps\Model\Front\User;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormPasswordChange
 * @package Apps\Model\Front\User
 */
class FormPasswordChange extends Model
{
    public $login;
    public $password;
    public $repassword;
    public $captcha;

    /** @var iUser */
    private $_user;

    /**
     * FormPasswordChange constructor. Pass user object inside
     * @param iUser $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        $this->login = $user->getParam('login');
        parent::__construct(true);
    }

    /**
     * Define model rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['password', 'repassword'], 'required'],
            ['password', 'length_min', 3],
            ['repassword', 'equal', $this->getRequest('password', $this->getSubmitMethod())],
            ['captcha', 'App::$Captcha::validate']
        ];
    }

    /**
     * Define view display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'password' => __('New password'),
            'repassword' => __('Repeat password'),
            'captcha' => __('Captcha')
        ];
    }

    public function make()
    {
        $this->_user->password = Crypt::passwordHash($this->password);
        $this->_user->save();
    }
}
