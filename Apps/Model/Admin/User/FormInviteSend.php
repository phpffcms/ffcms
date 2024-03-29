<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\Invite;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;

/**
 * Class FormInviteSend. Send user invitation to email
 * @package Apps\Model\Admin\User
 */
class FormInviteSend extends Model
{
    const TOKEN_VALID_TIME = 604800; // 7 days

    public $email;

    /**
     * Before execute method. Cleanup deprecated invites
     */
    public function before()
    {
        Invite::clean();
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'email' => __('Email')
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
            ['email', 'Apps\Model\Admin\User\FormUserUpdate::isUniqueEmail', null]
        ];
    }

    /**
     * Send invite to email
     * @return bool
     */
    public function make()
    {
        $token = $this->makeInvite();
        // save data in database
        $invObj = new Invite();
        $invObj->email = $this->email;
        $invObj->token = $token;
        $invObj->save();

        if (App::$Mailer->isEnabled()) {
            return App::$Mailer->tpl('user/_mail/invite', [
                'invite' => $token,
                'email' => $this->email
            ])->send($this->email, App::$Translate->get('Default', __('You got registration invite'), []));
        } else {
            App::$Debug->addMessage("Email features are disabled! No message sended!", "warning");
        }

        return false;
    }

    /**
     * Generate unique invite string
     * @return string
     */
    private function makeInvite()
    {
        $token = Crypt::randomString(mt_rand(32, 128));
        $find = Invite::where('token', $token)
            ->count();
        return $find === 0 ? $token : $this->makeInvite(); // prevent duplication
    }
}
