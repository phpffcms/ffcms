<?php

namespace Apps\Model\Front\Feedback;

use Apps\ActiveRecord\Ban;
use Apps\ActiveRecord\FeedbackAnswer;
use Apps\Model\Front\Profile\EntityAddNotification;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Exception\ForbiddenException;

/**
 * Class FormAnswerAdd. Model to work with add answer to feedback post thread
 * @package Apps\Model\Front\Feedback
 */
class FormAnswerAdd extends Model
{
    public $name;
    public $email;
    public $message;

    /** @var \Apps\ActiveRecord\FeedbackPost */
    protected $_post;
    protected $_userId;
    protected $_ip;

    /**
     * FormAnswerAdd constructor. Pass active record of comment post and user id
     * @param $recordPost
     */
    public function __construct($recordPost)
    {
        $this->_post = $recordPost;
        if (App::$User->isAuth()) {
            $this->_userId = App::$User->identity()->getId();
        }
        parent::__construct();
    }

    /**
     * Define local properties if user is authorized
     */
    public function before()
    {
        // check if client in ban list
        if (Ban::isBanned(App::$Request->getClientIp(), $this->_userId, true)) {
            throw new ForbiddenException(__('Sorry, but your account was banned!'));
        }

        if ($this->_userId > 0) {
            $user = App::$User->identity($this->_userId);
            $this->name = $user->profile->name;
            $this->email = $user->getParam('email');
        }
        $this->_ip = App::$Request->getClientIp();
    }

    /**
     * Labels for display form
     * @return array
     */
    public function labels(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'message' => __('Message')
        ];
    }

    /**
     * Form validation rules
     * @return array
     */
    public function rules(): array
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
        $record->name = $this->name;
        $record->email = $this->email;
        $record->message = $this->message;
        if ($this->_userId > 0) {
            $record->user_id = $this->_userId;
        }
        $record->ip = $this->_ip;
        $record->save();

        // add notification msg
        $targetId = $this->_post->user_id;
        if ($targetId !== null && (int)$targetId > 0 && $targetId !== $this->_userId) {
            $notify = new EntityAddNotification($targetId);
            $uri = '/feedback/read/' . $this->_post->id . '/' . $this->_post->hash . '#feedback-answer-' . $record->id;
            $notify->add($uri, EntityAddNotification::MSG_ADD_FEEDBACKANSWER, [
                'snippet' => Text::snippet($this->message, 50),
                'post' => Text::snippet($this->_post->message, 50)
            ]);
        }
    }
}
