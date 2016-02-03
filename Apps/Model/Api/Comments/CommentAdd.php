<?php

namespace Apps\Model\Api\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;

class CommentAdd extends Model
{
    public $pathway;
    public $message;
    public $replayTo = 0;
    public $guestName;
    public $ip;
    public $user_id = 0;

    public $response = [
        'status' => 0,
        'message' => 'unknown error'
    ];

    private $_configs = [];

    /**
     * CommentAdd constructor. Pass add comment data inside model
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Get and store to model properties other data
     */
    public function before()
    {
        $this->ip = App::$Request->getClientIp();
        if (App::$User->isAuth()) {
            $this->user_id = App::$User->identity()->getId();
        }
        parent::before();
    }

    public function check()
    {
        // check if user is auth'd or guest name is defined
        if (!App::$User->isAuth() && ((int)$this->_configs['guestAdd'] !== 1 || Str::length($this->guestName) < 2)) {
            throw new JsonException(__('Guest name is not defined'));
        }

        // check if pathway is empty
        if (Str::likeEmpty($this->pathway)) {
            throw new JsonException(__('Wrong target pathway'));
        }

        // check if message length is correct
        if (Str::length($this->message) < (int)$this->_configs['minLength'] || Str::length($this->message) > (int)$this->_configs['maxLength']) {
            throw new JsonException(__('Message length is incorrect. Current: %cur% , min - %min%, max - %max%', [
                'cur' => Str::length($this->message),
                'min' => $this->_configs['minLength'],
                'max' => $this->_configs['maxLength']
            ]));
        }

        // sounds like answer, lets try to find post thread comment
        if ($this->replayTo > 0) {
            $count = CommentPost::where('id', '=', $this->replayTo)->count();
            if ($count !== 1) {
                throw new JsonException(__('Comment post thread is not founded'));
            }
            // check for prevent spam
            $query = CommentAnswer::where(function($q) {
                $q->where('user_id', '=', $this->user_id)
                    ->orWhere('ip', '=', $this->ip);
            })->orderBy('created_at', 'DESC')
            ->first();

            // something is founded :D
            if ($query !== null) {
                $answerTime = Date::convertToTimestamp($query->created_at);
                $delay = $answerTime + $this->_configs['delay'] - time();
                if ($delay > 0) { // sounds like config time is not passed now
                    throw new JsonException(__('Spam protection: please, wait %sec% seconds', ['sec' => $delay]));
                }
            }
        } else { // sounds like post, lets try to check latest post
            $query = CommentPost::where(function($q) {
                $q->where('user_id', '=', $this->user_id)
                    ->orWhere('ip', '=', $this->ip);
            })->orderBy('created_at', 'DESC')
            ->first();

            // check if latest post time for this user is founded
            if ($query !== null) {
                $postTime = Date::convertToTimestamp($query->created_at);
                $delay = $postTime + $this->_configs['delay'] - time();
                if ($delay > 0) {
                    throw new JsonException(__('Spam protection: please, wait %sec% seconds', ['sec' => $delay]));
                }
            }
        }
    }

    public function make()
    {
        $record = null;
        $type = null;
        // sounds like answer
        if ($this->replayTo > 0) {
            $type = 'answer';
            $record = new CommentAnswer();
            $record->comment_id = $this->replayTo;
            $record->user_id = $this->user_id;
            $record->guest_name = $this->guestName;
            $record->message = $this->message;
            $record->ip = $this->ip;
            $record->save();
        } else { // sounds like new post
            $type = 'post';
            $record = new CommentPost();
            $record->pathway = $this->pathway;
            $record->user_id = $this->user_id;
            $record->guest_name = $this->guestName;
            $record->message = $this->message;
            $record->lang = App::$Request->getLanguage();
            $record->save();
        }


    }

}