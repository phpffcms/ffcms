<?php

namespace Apps\Model\Admin\User;


use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentRating;
use Apps\ActiveRecord\ContentTag;
use Apps\ActiveRecord\FeedbackPost;
use Apps\ActiveRecord\User;
use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Arch\Model;

/**
 * Class FormUserClear
 * @package Apps\Model\Admin\User
 */
class FormUserClear extends Model
{
    public $comments;
    public $content;
    public $feedback;
    public $wall;

    /** @var User */
    private $_user;

    /**
     * FormUserClear constructor. Pass user model inside
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->_user = $user;
        parent::__construct(true);
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['comments', 'content', 'feedback', 'wall'], 'required'],
            [['comments', 'content', 'feedback', 'wall'], 'boolean']
        ];
    }

    /**
     * Display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'comments' => __('Comments and answers'),
            'content' => __('Content'),
            'feedback' => __('Feedback requests'),
            'wall' => __('Wall posts and answers')
        ];
    }

    /**
     * Make delete
     * @throws \Exception
     */
    public function make()
    {
        if ((bool)$this->comments) {
            CommentPost::where('user_id', $this->_user->id)->delete();
            CommentAnswer::where('user_id', $this->_user->id)->delete();
        }

        if ((bool)$this->content) {
            $contents = Content::where('author_id', $this->_user->id);
            $ids = $contents->pluck('id')->toArray();
            if ($ids && count($ids) > 0) {
                ContentTag::whereIn('content_id', $ids)->delete();
                ContentRating::whereIn('content_id', $ids)->delete();
                $contents->delete();
            }
        }

        if ((bool)$this->feedback) {
            FeedbackPost::where('user_id', $this->_user->id)
                ->update(['readed' => true, 'closed' => true]);
        }

        if ((bool)$this->wall) {
            WallPost::where('sender_id', $this->_user->id)->delete();
            WallAnswer::where('user_id', $this->_user->id)->delete();
        }
    }

    /**
     * Get user object
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }
}