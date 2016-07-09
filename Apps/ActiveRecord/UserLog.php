<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Date;

/**
 * Class UserLog. Active record model.
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class UserLog extends ActiveModel
{
    /**
     * Get user object as relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User');
    }
}