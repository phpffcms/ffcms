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
 * @property string $created_at
 * @property string $updated_at
 */
class ContentTag extends ActiveModel
{
    
}