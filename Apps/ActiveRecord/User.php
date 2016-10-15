<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class User. Active record model for user auth data
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property string $approve_token
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveModel implements iUser
{
    private $openidProvider;

    /**
     * Get user object relation. If $user_id is null - get current session user
     * @param int|null $user_id
     * @return self|null
     */
    public static function identity($user_id = null)
    {
        if ($user_id === null) {
            $user_id = MainApp::$Session->get('ff_user_id');
        }

        // convert id to real integer
        $user_id = (int)$user_id;

        if (!Obj::isInt($user_id) || $user_id < 1) {
            return null;
        }

        // check in memory cache object
        if (MainApp::$Memory->get('user.object.cache.' . $user_id) !== null) {
            return MainApp::$Memory->get('user.object.cache.' . $user_id);
        }
        // not founded in memory? lets make query
        $user = self::find($user_id);
        // no rows? lets end this shit ;)
        if ($user === null || $user->id < 1) {
            return null;
        }

        // store cache and return object
        MainApp::$Memory->set('user.object.cache.' . $user->id, $user);
        return $user;
    }

    /**
     * Get current user id if auth
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Get user param
     * @param string $param
     * @param null|string $defaultValue
     * @return string|int|null
     */
    public function getParam($param, $defaultValue = null)
    {
        return $this->{$param} === null ? $defaultValue : $this->{$param};
    }

    /**
     * Check if current user session is auth
     * @return bool
     */
    public static function isAuth()
    {
        // get data from session
        $sessionUserId = (int)MainApp::$Session->get('ff_user_id', 0);

        // check if session contains user id data
        if ($sessionUserId < 1) {
            return false;
        }

        // find user identity
        $identity = self::identity($sessionUserId);
        if ($identity === null) { // check if this $id exist
            MainApp::$Session->invalidate(); // destory session data - it's not valid!
            return false;
        }

        // check if user is approved. Default value: 0, can be null, '' or the same.
        if ($identity->approve_token !== '0' && Str::length($identity->approve_token) > 0) {
            return false;
        }

        return ((int)$identity->id > 0 && (int)$identity->id === $sessionUserId);
    }

    /**
     * Check if user with $id exist
     * @param int $id
     * @return bool
     */
    public static function isExist($id)
    {
        if (!Obj::isLikeInt($id) || $id < 1) {
            return false;
        }

        // convert id to real integer
        $id = (int)$id;

        $find = MainApp::$Memory->get('user.counter.cache.' . $id);
        if ($find === null) {
            $find = self::where('id', '=', $id)->count();
            MainApp::$Memory->set('user.counter.cache.' . $id, $find);
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
        if (!Obj::isString($email) || !Str::isEmail($email)) {
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
        if (!Obj::isString($login) || Str::length($login) < 1) {
            return false;
        }

        return self::where('login', '=', $login)->count() > 0;
    }

    /**
     * Get user person like a object via email
     * @param string $email
     * @return null|static
     */
    public static function getIdentityViaEmail($email)
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
        return $this->hasMany('Apps\\ActiveRecord\\WallPost', 'target_id');
    }

    /**
     * Get user role object
     * @return \Apps\ActiveRecord\Role|null
     */
    public function getRole()
    {
        return Role::get($this->role_id);
    }

    /**
     * Get user profile data as relation of user table. Ex: User::find(1)->getProfile()->nick
     * @return \Apps\ActiveRecord\Profile
     */
    public function getProfile()
    {
        // lets find profile identity via current user id
        $object = Profile::identity($this->getId());
        // is not exist? Hmmm, lets create it!
        if ($object === null && $this->getId() > 0) {
            $object = new Profile();
            $object->user_id = $this->getId();
            $object->save();
        }
        // return result ;)
        return $object;
    }

    /**
     * Get user logs
     * @return \Apps\ActiveRecord\UserLog
     */
    public function getLogs()
    {
        return $this->hasMany('Apps\\ActiveRecord\\UserLog', 'user_id');
    }

    /**
     * Get user social providers data
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getProviders()
    {
        return $this->hasMany('Apps\ActiveRecord\UserProvider', 'user_id');
    }

    /**
     * Check if target user in blacklist
     * @param int $target_id
     * @return bool
     */
    public function inBlacklist($target_id)
    {
        return Blacklist::have($this->getId(), $target_id);
    }

    /**
     * Set openID library dependence object. Do not use this function, if you have no idia how it work
     * @param $provider
     */
    public function setOpenidInstance($provider)
    {
        $this->openidProvider = $provider;
    }

    /**
     * Get openid provider library. Default - hybridauth
     * @return \Hybrid_Auth
     */
    public function getOpenidInstance()
    {
        return $this->openidProvider;
    }
}