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
 * @property string $lang
 * @property string $ip
 * @property boolean $moderate
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property CommentPost $post
 */
class CommentAnswer extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'comment_id' => 'integer',
        'user_id' => 'integer',
        'guest_name' => 'string',
        'message' => 'string',
        'lang' => 'string',
        'ip' => 'string',
        'moderate' => 'boolean'
    ];

    /**
     * Get user relation object
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User', 'user_id');
    }

    /**
     * Get commentPost object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('Apps\ActiveRecord\CommentPost', 'comment_id');
    }

    /**
     * Get user identity
     * @return User|null
     * @deprecated
     */
    public function getUser()
    {
        return User::identity($this->user_id);
    }

    /**
     * Get comment post object
     * @return CommentPost|null
     * @deprecated
     */
    public function getCommentPost()
    {
        return CommentPost::where('id', '=', $this->comment_id)->first();
    }
}