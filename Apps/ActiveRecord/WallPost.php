<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

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