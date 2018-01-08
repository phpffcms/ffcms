<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class ContentTag. Active record object for content tags relation to content as many-to-one
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $content_id
 * @property string $lang
 * @property string $tag
 */
class ContentTag extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'content_id' => 'integer',
        'lang' => 'string',
        'tag' => 'string'
    ];
}
