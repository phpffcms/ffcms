<?php

namespace Apps\ActiveRecord;


use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserNotification. Active record model for user notifications
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property string $msg
 * @property string $uri
 * @property string $vars
 * @property int $readed
 * @property string $created_at
 * @property string $updated_at
 */
class UserNotification extends ActiveModel
{
    /**
     * Get user object as relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User');
    }
}