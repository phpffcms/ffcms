<?php

namespace Apps\Model\Api\Comments;


use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class CommentAnswerAdd. Model to add comment answer to database and build response active record row.
 * @package Apps\Model\Api\Comments
 */
class CommentAnswerAdd extends Model
{
    public $message;
    public $replayTo = 0;
    public $guestName;
    public $ip;
    
    private $_configs;
    private $_userId = 0;

    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    public function before()
    {
        $this->ip = App::$Request->getClientIp();
        if (App::$User->isAuth()) {
            $this->_userId = App::$User->identity()->getId();
        }
    }

    /**
     * Check if comment answer conditions is ok. Will throw exception if not.
     * @return bool
     * @throws JsonException
     */
    public function check()
    {
        // check if user is auth'd or guest name is defined
        if (!App::$User->isAuth() && ((int)$this->_configs['guestAdd'] !== 1 || Str::length($this->guestName) < 2)) {
            throw new JsonException(__('Guest name is not defined'));
        }

        if ($this->replayTo < 1) {
            throw new JsonException(__('Comment post thread is not founded'));
        }

        // check if message length is correct
        if (Str::length($this->message) < (int)$this->_configs['minLength'] || Str::length($this->message) > (int)$this->_configs['maxLength']) {
            throw new JsonException(__('Message length is incorrect. Current: %cur% , min - %min%, max - %max%', [
                'cur' => Str::length($this->message),
                'min' => $this->_configs['minLength'],
                'max' => $this->_configs['maxLength']
            ]));
        }

        $count = CommentPost::where('id', '=', $this->replayTo)->count();
        if ($count !== 1) {
            throw new JsonException(__('Comment post thread is not founded'));
        }

        // check to prevent spam
        $query = CommentAnswer::where('user_id', '=', $this->_userId)
            ->orWhere('ip', '=', $this->ip)
            ->orderBy('created_at', 'DESC')
            ->first();

        // something is founded :D
        if ($query !== null) {
            $answerTime = Date::convertToTimestamp($query->created_at);
            $delay = $answerTime + $this->_configs['delay'] - time();
            if ($delay > 0) { // sounds like config time is not passed now
                throw new JsonException(__('Spam protection: please, wait %sec% seconds', ['sec' => $delay]));
            }
        }

        return true;
    }

    /**
     * Add comment answer to database and return active record object
     * @return CommentAnswer
     */
    public function buildRecord()
    {
        $record = new CommentAnswer();
        $record->comment_id = $this->replayTo;
        $record->user_id = $this->_userId;
        $record->guest_name = $this->guestName;
        $record->message = $this->message;
        $record->ip = $this->ip;
        $record->save();

        return $record;
    }

}