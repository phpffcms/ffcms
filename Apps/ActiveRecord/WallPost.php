<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class WallPost. Active record model for user wall posts
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $target_id
 * @property int $sender_id
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property User $senderUser
 * @property User $targetUser
 * @property WallAnswer[] $answers
 */
class WallPost extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'target_id' => 'integer',
        'sender_id' => 'integer',
        'message' => 'string'
    ];

    /**
     * Get wall post answers relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany('Apps\ActiveRecord\WallAnswer', 'post_id');
    }

    /**
     * Get sender user object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function senderUser()
    {
        return $this->belongsTo('Apps\ActiveRecord\User', 'sender_id');
    }

    /**
     * Get target user object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetUser()
    {
        return $this->belongsTo('Apps\ActiveRecord\User', 'target_id');
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getWallAnswer()
    {
        return $this->answer();
    }

}