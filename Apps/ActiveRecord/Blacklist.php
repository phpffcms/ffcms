<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;

class Blacklist extends ActiveModel
{

    /**
     * Check if current user have in blacklist target_id user
     * @param int $target_id
     * @return bool
     */
    public static function have($user_id, $target_id)
    {
        $query = self::where('user_id', '=', $user_id)
            ->where('target_id', '=', $target_id);
        return $query->count() > 0;
    }

    /**
     * Check if user1 or user2 have in themself blacklists
     * @param int $user1
     * @param int $user2
     * @return bool
     */
    public static function check($user1, $user2)
    {
        $query = self::where(function($query) use ($user1, $user2){
            $query->where('user_id', '=', $user1)
                ->where('target_id', '=', $user2);
        })->orWhere(function($query) use ($user1, $user2){
            $query->where('user_id', '=', $user2)
                ->where('target_id', '=', $user1);
        });

        return $query->count() < 1;
    }

    /**
     * Get target user for current record
     * @return bool|\Illuminate\Support\Collection|null|static
     */
    public function getUser()
    {
        return App::$User->identity($this->target_id);
    }

}