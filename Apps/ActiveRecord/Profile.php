<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Interfaces\iProfile;
use Ffcms\Core\App as MainApp;

class Profile extends ActiveModel implements iProfile
{

    /**
     * Get user profile via user_id like object (!!! profile.id !== user.id !!!)
     * @param int|null $user_id
     * @return self|null
     */
    public static function identity($user_id = null)
    {
        if ($user_id === null) {
            $user_id = MainApp::$Session->get('ff_user_id');
        }

        if ($user_id === null || !Object::isLikeInt($user_id) || $user_id < 1) {
            return null;
        }

        // check in cache
        if (MainApp::$Memory->get('profile.object.cache.' . $user_id) !== null) {
            return MainApp::$Memory->get('profile.object.cache.' . $user_id);
        }

        // find row
        $profile = self::where('user_id', '=', $user_id);

        // empty? lets return null
        if (false === $profile || null === $profile || $profile->count() !== 1) {
            return null;
        }

        $object = $profile->first();

        MainApp::$Memory->set('profile.object.cache.' . $user_id, $object);
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
            return MainApp::$Alias->scriptUrl . $default;
        }

        $route = '/upload/user/avatar/' . $type . '/' . $this->user_id . '.jpg';
        if (File::exist($route)) {
            return MainApp::$Alias->scriptUrl . $route . '?mtime=' . File::mTime($route);
        }

        return MainApp::$Alias->scriptUrl . $default;
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