<?php

namespace Apps\Model\Basic;

use Apps\ActiveRecord\Profile as ProfileRecords;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Interfaces\iProfile;

class Profile extends ProfileRecords implements iProfile
{

    /**
     * Get user profile via user_id like object (!!! profile.id !== user.id !!!)
     * @param int|null $user_id
     * @return self|null
     */
    public static function identity($user_id = null)
    {
        if ($user_id === null) {
            $user_id = App::$Session->get('ff_user_id');
        }

        if ($user_id === null || !Object::isLikeInt($user_id) || $user_id < 1) {
            return null;
        }

        // check in cache
        if (App::$Memory->get('profile.object.cache.' . $user_id) !== null) {
            return App::$Memory->get('profile.object.cache.' . $user_id);
        }

        // find row
        $profile = self::where('user_id', '=', $user_id);

        // empty? lets return null
        if (false === $profile || null === $profile || $profile->count() !== 1) {
            return null;
        }

        $object = $profile->first();

        App::$Memory->set('profile.object.cache.' . $user_id, $object);
        return $object;
    }

    /**
     * Get user avatar full url for current object
     * @param string $type
     * @return string
     */
    public function getAvatarUrl($type = 'small')
    {
        $default = '/upload/user/avatar/' . $type . '/default.jpg';
        if (!Arr::in($type, ['small', 'big', 'medium'])) {
            return App::$Alias->scriptUrl . $default;
        }

        $route = '/upload/user/avatar/' . $type . '/' . $this->user_id . '.jpg';
        if (File::exist($route)) {
            return App::$Alias->scriptUrl . $route . '?mtime=' . File::mTime($route);
        }

        return App::$Alias->scriptUrl . $default;
    }

    /**
     * Get user identity for current object
     * @return User|null
     */
    public function User()
    {
        return User::identity($this->user_id);
    }
}