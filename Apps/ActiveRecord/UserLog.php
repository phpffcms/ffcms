<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserLog. Active record model.
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class UserLog extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'type' => 'string',
        'message' => 'string'
    ];

    /**
     * Get user object as relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User');
    }
}