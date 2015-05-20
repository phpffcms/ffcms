<?php

namespace Apps\Model\Basic;

use Apps\Model\ActiveRecord\User as ARecordUser;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Interfaces\iUser;

class User extends ARecordUser implements iUser
{

    protected $userCache;
    protected $counterCache;

    /**
     * Get current user id if auth
     * @return int|null
     */
    public function getId()
    {
        if (!$this->isAuth()) {
            return null;
        }
        return $this->get('id');
    }

    /**
     * Get user param
     * @param string $param
     * @param null|int $custom_id
     * @param null|string $defaultValue
     * @return string|int|null
     */
    public function get($param, $custom_id = null, $defaultValue = null)
    {
        $object = $this->getPerson($custom_id);
        if (false === $object || null === $object) { // false on incorrect type, null if not founded
            return null;
        }

        return $object->{$param} === null ? $defaultValue : $object->{$param};
    }

    /**
     * @param $param
     * @param null|int $custom_id
     * @param null|string $defaultValue
     * @return string|null
     */
    public function getCustomParam($param, $custom_id = null, $defaultValue = null)
    {
        $all = $this->get('custom_data', $custom_id);
        if ($all === null || String::length($all) < 2) { // must be a json-based type. Minimum: {}
            return null;
        }

        $customData = json_decode($all);
        return $customData->{$param} === null ? $defaultValue : $customData->{$param};
    }

    /**
     * Get user person all data like a object
     * @param null|int $user_id
     * @return bool|\Illuminate\Support\Collection|null|static
     */
    public function getPerson($user_id = null)
    {
        if ($user_id === null || Object::isInt($user_id)) {
            $user_id = App::$Session->get('ff_user_id');
        }

        if ($user_id === null || $user_id < 1) {
            return false;
        }

        // check in cache object
        if ($this->userCache[$user_id] !== null) {
            return $this->userCache[$user_id];
        }

        $user = self::find($user_id);
        if ($user !== false && $user !== null && $user->id > 0) {
            $this->userCache[$user->id] = $user;
            return $user;
        }

        return false;
    }

    /**
     * Check if current user session is auth
     * @return bool
     */
    public function isAuth()
    {
        App::$Session->start();
        $session_token = App::$Session->get('ff_user_token', null);
        $session_id = App::$Session->get('ff_user_id', 0);

        if (null === $session_token || !Object::isInt($session_id) || $session_id < 1 || String::length($session_token) < 64) {
            return false;
        }

        $find = $this->getPerson($session_id);
        if (null === $find || false === $find || String::length($find->token_data) < 64) { // check if this $id exist
            App::$Session->invalidate(); // destory session data - it's not valid!
            return false;
        }

        return $find->token_data === $session_token;
    }

    /**
     * Get user avatar full url
     * @param string $type
     * @param null|int $custom_id
     * @return string
     */
    public function getAvatarUrl($type = 'small', $custom_id = null)
    {
        $default = '/upload/user/avatar/' . $type . '/default.jpg';
        if (!$this->isExist($custom_id) || !Arr::in($type, ['small', 'big', 'medium'])) {
            return App::$Alias->scriptUrl . $default;
        }

        if ($custom_id === null) {
            $custom_id = $this->get('id');
        }
        $route = '/upload/user/avatar/' . $type . '/' . $custom_id . '.jpg';
        if (File::exist($route)) {
            return App::$Alias->scriptUrl . $route;
        }

        return App::$Alias->scriptUrl . $default;
    }

    /**
     * Check if user with $id exist
     * @param int $id
     * @return bool
     */
    public function isExist($id)
    {
        if (!Object::isInt($id) || $id < 1) {
            return false;
        }

        $find = $this->counterCache['id'][$id];
        if ($find === null) {
            $find = self::where('id', '=', $id)->count();
            $this->counterCache['id'][$id] = $find;
        }

        return $find === 1;
    }

    /**
     * Check if use with $email is exist
     * @param string $email
     * @return bool
     */
    public function isMailExist($email)
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
    public function isLoginExist($login)
    {
        if (!Object::isString($login) || String::length($login) < 1) {
            return false;
        }

        return self::where('login', '=', $login)->count() > 0;
    }

    /**
     * Get user person like a object via email
     * @param string $email
     * @return bool
     */
    public function getPersonViaEmail($email)
    {
        if (!$this->isMailExist($email)) {
            return false;
        }

        return self::where('email', '=', $email)->first();
    }

    /**
     * Get relation one-to-many for user wall posts. Ex: User::find(1)->getWall()->offset()
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getWall()
    {
        return $this->hasMany('Apps\\Model\\ActiveRecord\\Wall');
    }
}