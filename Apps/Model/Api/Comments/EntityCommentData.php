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
 * Class EntityCommentData. Unified model to get comment specified output to json response.
 * @package Apps\Model\Api\Comments
 */
class EntityCommentData extends Model
{
    /** @var \Apps\ActiveRecord\CommentPost|\Apps\ActiveRecord\CommentAnswer $_record */
    private $_record;
    private $_type;
    private $_calcAnswers = true;

    /**
     * EntityCommentPostData constructor. Pass inside the model active record object of comment post or answer
     * @param $record
     * @throws JsonException
     */
    public function __construct($record, $calcAnswers = true)
    {
        $this->_record = $record;
        $this->_calcAnswers = (bool)$calcAnswers;
        if ($this->_record instanceof CommentPost) {
            $this->_type = 'post';
        } elseif ($this->_record instanceof CommentAnswer) {
            $this->_type = 'answer';
        } else {
            throw new JsonException('Unknown comment request');
        }
        parent::__construct();
    }

    /**
     * Prepare output comment post information array
     * @return array|null
     */
    public function make(): ?array
    {
        if (!$this->_record) {
            return null;
        }

        // build user data
        $userName = __('Unknown');
        $userAvatar = App::$Alias->scriptUrl . '/upload/user/avatar/small/default.jpg';
        $userColor = 0;
        if ($this->_record->user !== null && $this->_record->user->id > 0) {
            $userName = $this->_record->user->profile->getNickname();
            $userAvatar = $this->_record->user->profile->getAvatarUrl('small');
            $userColor = $this->_record->user->role->color;
        } else {
            if (!Str::likeEmpty($this->_record->guest_name)) {
                $userName = App::$Security->strip_tags($this->_record->guest_name);
            }
        }

        // return output json data
        $res = [
            'type' => $this->_type,
            'id' => $this->_record->id,
            'text' => $this->_record->message,
            'date' => Date::convertToDatetime($this->_record->created_at, Date::FORMAT_TO_HOUR),
            'app_name' => $this->_record->app_name,
            'app_id' => $this->_record->app_relation_id,
            'moderate' => (int)$this->_record->moderate,
            'user' => [
                'id' => $this->_record->user_id,
                'name' => $userName,
                'avatar' => $userAvatar,
                'color' => $userColor
            ]
        ];

        if ($this->_type === 'post' && method_exists($this->_record, 'getAnswerCount') && $this->_calcAnswers) {
            $res['answers'] = $this->_record->getAnswerCount();
        } elseif ($this->_type === 'answer') {
            $res['comment_id'] = $this->_record->comment_id;
        }

        return $res;
    }
}
