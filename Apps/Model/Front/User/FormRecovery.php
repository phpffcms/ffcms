<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\UserLog;
use Apps\ActiveRecord\UserRecovery;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Helper\Date;

/**
 * Class FormRecovery. Model of business logic for user password recovery
 * @package Apps\Model\Front\User
 */
class FormRecovery extends Model
{
    const DELAY = 900; // delay between 2 recovery submits

    public $email;
    public $captcha;

    /**
     * Labels for visual display
     * @return array
     */
    public function labels(): array
    {
        return [
            'email' => __('Email'),
            'captcha' => __('Captcha')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['captcha', 'used'],
            ['captcha', 'App::$Captcha::validate'],
            ['email', 'App::$User::isMailExist']
        ];
    }

    /**
     * After validation generate new pwd, recovery token and send email
     * @throws SyntaxException
     */
    public function make()
    {
        $user = App::$User->getIdentityViaEmail($this->email);
        if ($user === null) {
            throw new SyntaxException('Email not found');
        }

        if ($user->approve_token) {
            throw new SyntaxException('You must approve your account');
        }

        $rows = UserRecovery::where('user_id', '=', $user->getId())
            ->orderBy('id', 'DESC')
            ->first();

        if ($rows !== null && $rows !== false) {
            // prevent spam of recovery messages
            if (Date::convertToTimestamp($rows->created_at) > time() - self::DELAY) {
                return;
            }
        }

        // generate random token key chr[128]
        $token = Crypt::randomString(mt_rand(64, 127));

        // write new data to recovery table
        $rObject = new UserRecovery();
        $rObject->user_id = $user->id;
        $rObject->token = $token;
        $rObject->save();

        // write logs data
        $log = new UserLog();
        $log->user_id = $user->id;
        $log->type = 'RECOVERY';
        $log->message = __('Password recovery is initialized from: %ip%', ['ip' => App::$Request->getClientIp()]);
        $log->save();

        if (App::$Mailer) {
            // send recovery email
            App::$Mailer->tpl('user/_mail/recovery', [
                'login' => $user->login,
                'email' => $this->email,
                'token' => $token,
                'id' => $rObject->id
            ])->send($this->email, App::$Translate->get('Profile', '%site% - account recovery', ['site' => App::$Request->getHost()]));
        }
    }
}
