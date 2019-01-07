<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;
use Ffcms\Core\Traits\SearchableTrait;

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
 * @property WallPost $wall
 * @property Profile|null $profile
 * @property Role $role
 * @property UserLog $log
 * @property UserProvider $provider
 */
class User extends ActiveModel implements iUser
{
    use SearchableTrait;

    protected $casts = [
        'id' => 'integer',
        'login' => 'string',
        'email' => 'string',
        'role_id' => 'integer',
        'approve_token' => 'string'
    ];

    protected $searchable = [
        'columns' => [
            'login' => 2,
            'email' => 3,
            'nick' => 1
        ],
        'joins' => [
            'profiles' => ['users.id', 'profiles.user_id']
        ]
    ];

    private $openidProvider;

    /**
     * Get user object relation. If $user_id is null - get current session user
     * @param string|int|null $id
     * @return self|null
     */
    public static function identity(?string $id = null): ?self
    {
        if (!$id) {
            $id = MainApp::$Session->get('ff_user_id');
        }

        // check if id is looks like integer
        if (!Any::isInt($id) || (int)$id < 1) {
            return null;
        }

        // check in memory cache object
        if (MainApp::$Memory->get('user.object.cache.' . $id)) {
            return MainApp::$Memory->get('user.object.cache.' . $id);
        }

        // not founded in memory? lets make query
        $user = self::with(['profile', 'role'])
            ->find($id);

        // store cache and return object
        MainApp::$Memory->set('user.object.cache.' . $user->id, $user);
        return $user;
    }

    /**
     * Get current user id if auth
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * Get user param
     * @param string $param
     * @param null|string $defaultValue
     * @return string|int|null
     */
    public function getParam(string $param, ?string $defaultValue = null): ?string
    {
        return $this->{$param} ?? $defaultValue;
    }

    /**
     * Check if current user session is auth
     * @return bool
     */
    public static function isAuth(): bool
    {
        // get data from session
        $sessionUserId = (int)MainApp::$Session->get('ff_user_id', 0);

        // check if session contains user id data
        if ($sessionUserId < 1) {
            return false;
        }

        // find user identity
        $identity = self::identity($sessionUserId);
        if (!$identity) { // check if this $id exist
            MainApp::$Session->invalidate(); // destory session data - it's not valid!
            return false;
        }

        // check if user is approved. Default value: 0, can be null, '' or the same.
        if ($identity->approve_token) {
            return false;
        }

        return ($identity->id > 0 && $identity->id === $sessionUserId);
    }

    /**
     * Check if user with $id exist
     * @param string|int|null $id
     * @return bool
     */
    public static function isExist(?string $id = null): bool
    {
        if (!$id || !Any::isInt($id)) {
            return false;
        }

        $find = MainApp::$Memory->get('user.counter.cache.' . $id);
        if (!$find) {
            $find = self::where('id', $id)->count();
            MainApp::$Memory->set('user.counter.cache.' . $id, $find);
        }

        return (int)$find === 1;
    }

    /**
     * Check if use with $email is exist
     * @param string $email
     * @return bool
     */
    public static function isMailExist(?string $email = null): bool
    {
        if (!Any::isStr($email) || !Str::isEmail($email)) {
            return false;
        }

        return self::where('email', $email)->count() > 0;
    }

    /**
     * Check if user with $login is exist
     * @param string $login
     * @return bool
     */
    public static function isLoginExist(?string $login = null): bool
    {
        if (!Any::isStr($login) || Any::isEmpty($login) || Str::length($login) < 2) {
            return false;
        }

        return self::where('login', $login)->count() > 0;
    }

    /**
     * Get user person like a object via email
     * @param string|null $email
     * @return null|self
     */
    public static function getIdentityViaEmail(?string $email = null)
    {
        if (!self::isMailExist($email)) {
            return null;
        }

        return self::where('email', $email)->first();
    }

    /**
     * Get user wall post relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wall()
    {
        return $this->hasMany(WallPost::class, 'target_id');
    }

    /**
     * Get user role relation object.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    /**
     * Get user profile relation object.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    /**
     * Get user logs relation object
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function log()
    {
        return $this->hasMany(UserLog::class, 'user_id');
    }

    /**
     * Get user social providers data
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function provider()
    {
        return $this->hasMany(UserProvider::class, 'user_id');
    }

    /**
     * Check if target user in blacklist
     * @param string|int|null $target
     * @return bool
     */
    public function inBlacklist(?string $target = null): bool
    {
        if (!$target || (int)$target < 1) {
            return false;
        }

        return Blacklist::have($this->getId(), $target);
    }

    /**
     * Set openID library dependence object. Do not use this function, if you have no idia how it work
     * @param $provider
     */
    public function setOpenidInstance($provider): void
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
