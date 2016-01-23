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
 * @property int $readed
 * @property string $created_at
 * @property string $updated_at
 */
class Message extends ActiveModel
{

}