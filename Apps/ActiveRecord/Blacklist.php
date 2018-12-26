<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;

/**
 * Class Blacklist. Active record for user blacklist table
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property int $target_id
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 * @property User $targetUser
 */
class Blacklist extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'target_id' => 'integer',
        'comment' => 'string'
    ];

    /**
     * Check if current user have in blacklist target_id user
     * @param int $currentId
     * @param int $targetId
     * @return bool
     */
    public static function have($currentId, $targetId): bool
    {
        return self::where('user_id', $currentId)
            ->where('target_id', $targetId)
            ->count() > 0;
    }

    /**
     * Check if user1 or user2 have in themself blacklists
     * @param int $user1
     * @param int $user2
     * @return bool
     */
    public static function check($user1, $user2): bool
    {
        $query = self::where(function ($query) use ($user1, $user2) {
            $query->where('user_id', $user1)
                ->where('target_id', $user2);
        })->orWhere(function ($query) use ($user1, $user2) {
            $query->where('user_id', $user2)
                ->where('target_id', $user1);
        });

        return $query->count() < 1;
    }

    /**
     * Get target user object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
