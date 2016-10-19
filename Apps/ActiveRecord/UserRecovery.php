<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserRecovery. Active record model for user recoveries requests
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property string $password
 * @property string $token
 * @property bool $archive
 * @property string $created_at
 * @property string $updated_at
 */
class UserRecovery extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'password' => 'string',
        'token' => 'string',
        'archive' => 'boolean'
    ];

}