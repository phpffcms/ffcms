<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class CommentPost. Active record model for comment posts.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $pathway
 * @property int $user_id
 * @property string|null $guest_name
 * @property string $message
 * @property string $lang
 * @property string $created_at
 * @property string $updated_at
 */
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