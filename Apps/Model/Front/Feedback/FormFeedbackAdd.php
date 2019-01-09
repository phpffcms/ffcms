<?php

namespace Apps\Model\Front\Feedback;

use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;

/**
 * Class FormFeedbackAdd. Add new feedback request business logic model
 * @package Apps\Model\Front\Feedback
 */
class FormFeedbackAdd extends Model
{
    public $name;
    public $email;
    public $captcha;
    public $message;

    private $_hash;

    private $_useCaptcha = true;

    /**
     * FormFeedbackAdd constructor. Pass captcha marker enabled inside
     * @param bool $captcha
     */
    public function __construct($captcha = true)
    {
        $this->_useCaptcha = (bool)$captcha;
        parent::__construct();
    }


    /**
     * Set user data if he is logged in
     */
    public function before()
    {
        if (App::$User->isAuth()) {
            $data = App::$User->identity();
            $this->name = $data->profile->nick;
            $this->email = $data->email;
        }
    }

    /**
     * Labels to display form
     * @return array
     */
    public function labels(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'message' => __('Message'),
            'captcha' => __('Captcha')
        ];
    }

    /**
     * Rules to validate send form
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['name', 'email', 'message'], 'required'],
            ['name', 'length_min', '2'],
            ['message', 'length_min', 10],
            ['email', 'email'],
            ['captcha', 'used']
        ];
        if (true === $this->_useCaptcha) {
            $rules[] = ['captcha', 'App::$Captcha::validate'];
        }
        return $rules;
    }

    /**
     * Process submit new request
     * @return FeedbackPost
     */
    public function make()
    {
        // calculate security hash to direct-on access
        $hash = Crypt::randomString(mt_rand(16, 64));

        // init new row and set row data
        $record = new FeedbackPost();
        $record->name = $this->name;
        $record->email = $this->email;
        $record->message = $this->message;
        $record->hash = $hash;
        if (App::$User->isAuth()) {
            $record->user_id = App::$User->identity()->getId();
        }

        $record->ip = App::$Request->getClientIp();
        // save row to db
        $record->save();

        if (App::$Mailer) {
            // send notification to email
            App::$Mailer->tpl('feedback/_mail/created', [
                'record' => $record
            ])->send($record->email, App::$Translate->get('Feedback', 'Request #%id% is created', ['id' => $record->id]));
        }

        return $record;
    }

    /**
     * @return string|null
     */
    public function getHash()
    {
        return $this->_hash;
    }
}
