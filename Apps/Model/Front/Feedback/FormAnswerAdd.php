<?php

namespace Apps\Model\Front\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;

/**
 * Class FormAnswerAdd. Model to work with add answer to feedback post thread
 * @package Apps\Model\Front\Feedback
 */
class FormAnswerAdd extends Model
{
    public $name;
    public $email;
    public $message;

    /** @var \Apps\ActiveRecord\FeedbackPost $_post */
    protected $_post;
    protected $_userId;
    protected $_ip;

    /**
     * FormAnswerAdd constructor. Pass active record of comment post and user id
     * @param $recordPost
     * @param int $userId
     */
    public function __construct($recordPost, $userId = 0)
    {
        $this->_post = $recordPost;
        $this->_userId = (int)$userId;
        parent::__construct();
    }

    /**
     * Define local properties if user is authorized
     */
    public function before()
    {
        if ($this->_userId > 0) {
            $user = App::$User->identity($this->_userId);
            $this->name = $user->getProfile()->nick;
            $this->email = $user->getParam('email');
        }
        $this->_ip = App::$Request->getClientIp();
    }

    /**
    * Labels for display form
    */
    public function labels()
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'message' => __('Message')
        ];
    }

    /**
    * Form validation rules
    */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            ['name', 'length_min', 2],
            ['email', 'email'],
            ['message', 'length_min', 10]
        ];
    }

    /**
     * Add new row to database and set post is unreaded
     */
    public function make()
    {
        // update readed marker
        $this->_post->readed = 0;
        $this->_post->save();

        // add new answer row in database
        $record = new FeedbackAnswer();
        $record->feedback_id = $this->_post->id;
        $record->name = App::$Security->strip_tags($this->name);
        $record->email = App::$Security->strip_tags($this->email);
        $record->message = App::$Security->strip_tags($this->message);
        if ($this->_userId > 0) {
            $record->user_id = $this->_userId;
        }
        $record->ip = $this->_ip;
        $record->save();
    }
}