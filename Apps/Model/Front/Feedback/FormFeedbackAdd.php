<?php

namespace Apps\Model\Front\Feedback;

use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Str;

class FormFeedbackAdd extends Model
{
    public $name;
    public $email;
    public $captcha;
    public $message;

    private $_hash;

    private $_useCaptcha = true;

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
            $this->name = $data->getProfile()->nick;
            $this->email = $data->email;
        }
    }

    /**
    * Labels to display form
    */
    public function labels()
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
    */
    public function rules()
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

    public function make()
    {
        // calculate security hash to direct-on access
        $hash = Str::randomLatinNumeric(mt_rand(16, 64));

        // init new row and set row data
        $record = new FeedbackPost();
        $record->name = App::$Security->strip_tags($this->name);
        $record->email = App::$Security->strip_tags($this->email);
        $record->message = App::$Security->strip_tags($this->message);
        $record->hash = $hash;
        if (App::$User->isAuth()) {
            $record->user_id = App::$User->identity()->getId();
        }

        $record->ip = App::$Request->getClientIp();
        // save row to db
        $record->save();

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