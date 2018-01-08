<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class ContentCategory. Active record model for content category nesting
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $content_id
 * @property int $user_id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 */
class ContentRating extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'content_id' => 'integer',
        'user_id' => 'integer',
        'type' => 'string'
    ];
}
