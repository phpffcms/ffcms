<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserRecovery. Active record model for user recoveries requests
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property bool $archive
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserRecovery extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'token' => 'string',
        'archive' => 'boolean'
    ];

    /**
     * Get relation to user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
