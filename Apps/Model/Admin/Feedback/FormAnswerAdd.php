<?php

namespace Apps\Model\Admin\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\ActiveRecord\FeedbackPost;
use Apps\Model\Front\Feedback\FormAnswerAdd as FrontAnswer;
use Apps\Model\Front\Profile\EntityAddNotification;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Text;

/**
 * Class FormAnswerAdd. Extend front model add answer
 * @package Apps\Model\Admin\Feedback
 */
class FormAnswerAdd extends FrontAnswer
{
    /**
     * @inheritdoc
     */
    public function make()
    {
        // update readed marker
        $this->_post->readed = 1;
        $this->_post->save();

        // add new answer row in database
        $record = new FeedbackAnswer();
        $record->feedback_id = $this->_post->id;
        $record->name = $this->name;
        $record->email = $this->email;
        $record->message = $this->message;
        $record->user_id = $this->_userId;
        $record->is_admin = 1;

        $record->ip = $this->_ip;
        $record->save();

        // add user notification
        if ((int)$this->_post->user_id > 0 && $this->_userId !== (int)$this->_post->user_id) {
            $notify = new EntityAddNotification((int)$this->_post->user_id);
            $uri = '/feedback/read/' . $this->_post->id . '/' . $this->_post->hash . '#feedback-answer-' . $record->id;

            $notify->add($uri, EntityAddNotification::MSG_ADD_FEEDBACKANSWER, [
                'snippet' => Text::snippet($this->message, 50),
                'post' => Text::snippet($this->_post->message, 50)
            ]);
        }

        // send email notification
        App::$Mailer->tpl('feedback/mail/newanswer', [
            'record' => $record
        ])->send($record->email, App::$Translate->get('Feedback', 'New answer in request #%id%', ['id' => $record->id]));

        // unset message data
        $this->message = null;
    }
}