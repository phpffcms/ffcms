<?php

namespace Apps\Model\Basic;

use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\User as ARecordUser;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Interfaces\iUser;

class User extends ARecordUser implements iUser
{

    public static $instance;

    /**
     * Get user object relation. If $user_id is null - get current session user
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

        // check in memory cache object
        if (App::$Memory->get('user.object.cache.' . $user_id) !== null) {
            self::$instance[$user_id] = App::$Memory->get('user.object.cache.' . $user_id);
        } else {
            $user = self::find($user_id);
            if ($user !== false && $user !== null && $user->id > 0) {
                App::$Memory->set('user.object.cache.' . $user->id, $user);
                self::$instance[$user_id] = $user;
            }
        }

        return self::$instance[$user_id];
    }



    /**
     * Get current user id if auth
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get user param
     * @param string $param
     * @param null|string $defaultValue
     * @return string|int|null
     */
    public function get($param, $defaultValue = null)
    {
        return $this->{$param} === null ? $defaultValue : $this->{$param};
    }

    /**
     * @param $param
     * @param null|string $defaultValue
     * @return string|null
     */
    public function getCustomParam($param, $defaultValue = null)
    {
        $all = $this->custom_data;
        if ($all === null || String::length($all) < 2) { // must be a json-based type. Minimum: {}
            return null;
        }

        $customData = json_decode($all);
        return $customData->{$param} === null ? $defaultValue : $customData->{$param};
    }

    /**
     * Check if current user session is auth
     * @return bool
     */
    public static function isAuth()
    {
        $session_token = App::$Session->get('ff_user_token', null);
        $session_id = App::$Session->get('ff_user_id', 0);

        if (null === $session_token || !Object::isInt($session_id) || $session_id < 1 || String::length($session_token) < 64) {
            return false;
        }

        $find = self::identity($session_id);
        if (null === $find || String::length($find->token_data) < 64) { // check if this $id exist
            App::$Session->invalidate(); // destory session data - it's not valid!
            return false;
        }

        return $find->token_data === $session_token;
    }

    /**
     * Get user avatar full url
     * @param string $type
     * @return string
     */
    public function getAvatarUrl($type = 'small')
    {
        $default = '/upload/user/avatar/' . $type . '/default.jpg';
        if (!Arr::in($type, ['small', 'big', 'medium'])) {
            return App::$Alias->scriptUrl . $default;
        }

        $route = '/upload/user/avatar/' . $type . '/' . $this->id . '.jpg';
        if (File::exist($route)) {
            return App::$Alias->scriptUrl . $route . '?mtime=' . File::mTime($route);
        }

        return App::$Alias->scriptUrl . $default;
    }

    /**
     * Check if user with $id exist
     * @param int $id
     * @return bool
     */
    public static function isExist($id)
    {
        if (!Object::isLikeInt($id) || $id < 1) {
            return false;
        }

        $find = App::$Memory->get('user.counter.cache.' . $id);
        if ($find === null) {
            $find = self::where('id', '=', $id)->count();
            App::$Memory->set('user.counter.cache.' . $id, $find);
        }

        return $find === 1;
    }

    /**
     * Check if use with $email is exist
     * @param string $email
     * @return bool
     */
    public static function isMailExist($email)
    {
        if (!Object::isString($email) || !String::isEmail($email)) {
            return false;
        }

        return self::where('email', '=', $email)->count() > 0;
    }

    /**
     * Check if user with $login is exist
     * @param string $login
     * @return bool
     */
    public static function isLoginExist($login)
    {
        if (!Object::isString($login) || String::length($login) < 1) {
            return false;
        }

        return self::where('login', '=', $login)->count() > 0;
    }

    /**
     * Get user person like a object via email
     * @param string $email
     * @return null|static
     */
    public static function getIdentifyViaEmail($email)
    {
        if (!self::isMailExist($email)) {
            return null;
        }

        return self::where('email', '=', $email)->first();
    }

    /**
     * Get relation one-to-many for user wall posts. Ex: User::find(1)->getWall()->offset()
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getWall()
    {
        return $this->hasMany('Apps\\ActiveRecord\\Wall', 'target_id');
    }

    /**
     * Get user role object
     * @return \Apps\ActiveRecord\Role|null
     */
    public function getRole()
    {
        return (new Role())->get($this->role_id);
    }
}