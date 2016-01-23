<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class WallPost. Active record model for user wall posts
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $target_id
 * @property int $sender_id
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class WallPost extends ActiveModel
{

    /**
     * Get wall post answers relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAnswer()
    {
        return $this->hasMany('Apps\\ActiveRecord\\WallAnswer', 'post_id');
    }

}