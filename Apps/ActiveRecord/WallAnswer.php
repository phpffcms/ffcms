<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class WallAnswer. Active record model for wall answers for wall posts
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property WallPost $post
 */
class WallAnswer extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'user_id' => 'integer',
        'message' => 'string'
    ];

    /**
     * Get user object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User', 'user_id');
    }

    /**
     * Get wall post object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('Apps\ActiveRecord\WallPost', 'post_id');
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
     * Get wall post object
     * @return WallPost|null
     * @deprecated
     */
    public function getWallPost()
    {
        return WallPost::where('id', '=', $this->post_id)->first();
    }
}
