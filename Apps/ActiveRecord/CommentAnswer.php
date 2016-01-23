<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class CommentAnswer. Active record model for comments answers list
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $comment_id
 * @property int $user_id
 * @property string $guest_name
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class CommentAnswer extends ActiveModel
{

    /**
     * Get user identity
     * @return User|null
     */
    public function getUser()
    {
        return User::identity($this->user_id);
    }

    /**
     * Get comment post object
     * @return WallPost|null
     */
    public function getCommentPost()
    {
        return CommentPost::where('id', '=', $this->comment_id)->first();
    }
}