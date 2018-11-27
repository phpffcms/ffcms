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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get wall post object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(WallPost::class, 'post_id');
    }
}
