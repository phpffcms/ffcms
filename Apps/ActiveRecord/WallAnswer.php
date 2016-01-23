<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class WallAnswer. Active record model for wall answers for wall posts
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
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