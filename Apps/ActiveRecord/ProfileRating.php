<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class ProfileRating. Active record model for user rating changes logging
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $target_id
 * @property int $sender_id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 */
class ProfileRating extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'target_id' => 'integer',
        'sender_id' => 'integer',
        'type' => 'string'
    ];
}