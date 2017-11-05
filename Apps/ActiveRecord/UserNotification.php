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
 * @property array $vars
 * @property bool $readed
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserNotification extends ActiveModel
{
    public $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'msg' => 'string',
        'uri' => 'string',
        'vars' => 'serialize',
        'readed' => 'boolean'
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