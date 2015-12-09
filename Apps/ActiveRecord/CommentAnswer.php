<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

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