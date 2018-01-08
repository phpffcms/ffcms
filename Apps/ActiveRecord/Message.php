<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class Message. Active record model for user messages list
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $target_id
 * @property int $sender_id
 * @property string $message
 * @property bool $readed
 * @property string $created_at
 * @property string $updated_at
 */
class Message extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'target_id' => 'integer',
        'sender_id' => 'integer',
        'message' => 'string',
        'readed' => 'boolean'
    ];
}
