<?php

namespace Apps\ActiveRecord;


use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserProvider. Active record model of user social providers info.
 * @property int $id
 * @property int $user_id
 * @property string $provider_name
 * @property string $provider_id
 * @property string $created_at
 * @property string $updated_at
 * @package Apps\ActiveRecord
 */
class UserProvider extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'provider_name' => 'string',
        'provider_id' => 'string'
    ];

    /**
     * Define relation from openid providers to user table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User');
    }
}