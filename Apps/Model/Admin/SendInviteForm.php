<?php

namespace Apps\Model\Admin;

use Apps\ActiveRecord\Invite;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\String;

class SendInviteForm extends Model
{
    public $email;

    const TOKEN_VALID_TIME = 604800; // 7 days

    public function before()
    {
        Invite::clean();
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function labels()
    {
        return [
            'email' => __('Email')
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validate()
    */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'Apps\Model\Admin\UserUpdateForm::isUniqueEmail', null]
        ];
    }

    public function make()
    {
        $token = $this->makeInvite();
        // save data in database
        $invObj = new Invite();
        $invObj->email = $this->email;
        $invObj->token = $token;
        $invObj->save();

        // get mailing template
        $template = App::$View->show('user/_inviteMail', [
            'invite' => $token,
            'email' => $this->email
        ]);
        $sender = App::$Property->get('adminEmail');

        // format SWIFTMailer format
        $mailMessage = \Swift_Message::newInstance(App::$Translate->get('Default', 'You got invite', []))
            ->setFrom([$sender])
            ->setTo([$this->email])
            ->setBody($template);
        // send message
        return App::$Mailer->send($mailMessage);
    }

    private function makeInvite()
    {
        $token = String::randomLatinNumeric(rand(32, 128));
        $find = Invite::where('token', '=', $token)->count();
        return $find === 0 ? $token : $this->makeInvite(); // prevent duplication
    }
}