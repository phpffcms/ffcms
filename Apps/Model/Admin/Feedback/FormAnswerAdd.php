<?php

namespace Apps\Model\Admin\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\Model\Front\Feedback\FormAnswerAdd as FrontAnswer;
use Ffcms\Core\App;

/**
 * Class FormAnswerAdd. Extend front model add answer
 * @package Apps\Model\Admin\Feedback
 */
class FormAnswerAdd extends FrontAnswer
{
    public function make()
    {
        // update readed marker
        $this->_post->readed = 1;
        $this->_post->save();

        // add new answer row in database
        $record = new FeedbackAnswer();
        $record->feedback_id = $this->_post->id;
        $record->name = App::$Security->strip_tags($this->name);
        $record->email = App::$Security->strip_tags($this->email);
        $record->message = App::$Security->strip_tags($this->message);
        $record->user_id = $this->_userId;
        $record->is_admin = 1;

        $record->ip = $this->_ip;
        $record->save();
    }
}