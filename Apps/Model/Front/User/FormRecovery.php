<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\UserRecovery;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Apps\ActiveRecord\UserLog;

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
    public function labels()
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
    public function rules()
    {
        return [
            [['email', 'captcha'], 'required'],
            ['email', 'email'],
            ['captcha', 'App::$Captcha::validate'],
            ['email', 'App::$User::isMailExist'],
        ];
    }

    /**
     * After validation generate new pwd, recovery token and send email
     * @throws SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function make()
    {
        $user = App::$User->getIdentityViaEmail($this->email);
        if ($user === null) {
            throw new SyntaxException('Email not found');
        }
        if ($user->approve_token !== '0' && Str::length($user->approve_token) > 0) {
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

        // generate pwd, token and pwdCrypt
        $newPwd = Str::randomLatinNumeric(mt_rand(8, 16));
        $pwdCrypt = App::$Security->password_hash($newPwd);
        $token = Str::randomLatinNumeric(mt_rand(64, 128));

        // write new data to recovery table
        $rObject = new UserRecovery();
        $rObject->user_id = $user->id;
        $rObject->password = $pwdCrypt;
        $rObject->token = $token;
        $rObject->save();

        // write logs data
        $log = new UserLog();
        $log->user_id = $user->id;
        $log->type = 'RECOVERY';
        $log->message = __('Password recovery is initialized from: %ip%', ['ip' => App::$Request->getClientIp()]);
        $log->save();

        // generate mail template
        $mailTemplate = App::$View->render('user/mail/recovery', [
            'login' => $user->login,
            'email' => $this->email,
            'password' => $newPwd,
            'token' => $token,
            'id' => $rObject->id
        ]);

        $sender = App::$Properties->get('adminEmail');

        // format SWIFTMailer format
        $mailMessage = \Swift_Message::newInstance(App::$Translate->get('Profile', 'Account recovery on %site%', ['site' => App::$Request->getHost()]))
            ->setFrom([$sender])
            ->setTo([$this->email])
            ->setBody($mailTemplate, 'text/html');
        // send message
        App::$Mailer->send($mailMessage);
    }
}