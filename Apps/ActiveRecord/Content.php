<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends ActiveModel
{
    use SoftDeletes;

    /**
     * Get category relation of this content id
     * @return ContentCategory|null|object
     */
    public function getCategory()
    {
        return ContentCategory::getById($this->category_id);
    }
}