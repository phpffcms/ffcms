<?php

namespace Apps\Model\Api\Comments;


use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class CommentPostAdd. Model to parse and insert input comment post data.
 * @package Apps\Model\Api\Comments
 */
class CommentPostAdd extends Model
{
    public $pathway;
    public $message;
    public $guestName;

    public $ip;

    private $_configs;
    private $_userId = 0;

    /**
     * CommentPostAdd constructor. Pass configuration inside.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Prepare model data - user ip and other data
     */
    public function before()
    {
        // set user ip
        $this->ip = App::$Request->getClientIp();
        // set user object if auth done
        if (App::$User->isAuth()) {
            $this->_userId = App::$User->identity()->getId();
        }
    }

    /**
     * Check comment add conditions. On bad conditions will be throw'd exception.
     * @throws JsonException
     * @return boolean
     */
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

        // check delay between 2 comments from 1 user or 1 ip
        $query = CommentPost::where('user_id', '=', $this->_userId)
            ->orWhere('ip', '=', $this->ip)
            ->orderBy('created_at', 'DESC')
            ->first();

        // check if latest post time for this user is founded
        if ($query !== null) {
            $postTime = Date::convertToTimestamp($query->created_at);
            $delay = $postTime + $this->_configs['delay'] - time();
            if ($delay > 0) {
                throw new JsonException(__('Spam protection: please, wait %sec% seconds', ['sec' => $delay]));
            }
        }

        return true;
    }

    /**
     * Insert new comment in table and return active record object
     * @return CommentPost
     */
    public function buildRecord()
    {
        $record = new CommentPost();
        $record->pathway = $this->pathway;
        $record->user_id = $this->_userId;
        $record->guest_name = $this->guestName;
        $record->message = $this->message;
        $record->lang = App::$Request->getLanguage();
        $record->save();

        return $record;
    }
}