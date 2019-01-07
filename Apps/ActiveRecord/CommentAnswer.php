<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Traits\SearchableTrait;

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
    use SearchableTrait;

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

    protected $searchable = [
        'columns' => [
            'message' => 1
        ]
    ];

    /**
     * Get user relation object
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get commentPost object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(CommentPost::class, 'comment_id');
    }
}
