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
        $this->sendEmail($this->_post);

        // unset message data
        $this->message = null;
    }

    /**
     * Send notification to post owner
     * @param FeedbackPost $record
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function sendEmail($record)
    {
        // prepare email template
        $template = App::$View->render('feedback/mail/newanswer', [
            'record' => $record
        ]);

        // get website default email
        $sender = App::$Properties->get('adminEmail');

        // build swift mailer handler
        $mailMessage = \Swift_Message::newInstance(App::$Translate->get('Feedback', 'New answer in request #%id%', ['id' => $record->id]))
            ->setFrom([$sender])
            ->setTo([$record->email])
            ->setBody($template, 'text/html');
        // send message over swift instance
        App::$Mailer->send($mailMessage);
    }
}