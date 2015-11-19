<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

class CommentPost extends ActiveModel
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
     * Get comment post->answers relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAnswer()
    {
        return $this->hasMany('Apps\\ActiveRecord\\CommentAnswer', 'comment_id');
    }

    /**
     * Get answers count for post comment id
     * @return int
     */
    public function getAnswerCount()
    {
        return CommentAnswer::where('comment_id', '=', $this->id)->count();
    }

}