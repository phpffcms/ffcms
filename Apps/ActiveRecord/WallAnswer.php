<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

class WallAnswer extends ActiveModel
{

    /**
     * Get user identity
     * @return User|null
     */
    public function getUser()
    {
        return User::identity($this->user_id);
    }

    /**
     * Get wall post object
     * @return WallPost|null
     */
    public function getWallPost()
    {
        return WallPost::where('id', '=', $this->post_id)->first();
    }
}