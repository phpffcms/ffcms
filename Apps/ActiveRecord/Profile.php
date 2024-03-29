<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Database\Serialize;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iProfile;

/**
 * Class Profile. Active record model for user profile data store
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $sex
 * @property string $birthday
 * @property string $city
 * @property string $hobby
 * @property int $rating
 * @property string $phone
 * @property string $url
 * @property string $about
 * @property array $custom_data
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class Profile extends ActiveModel implements iProfile
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'name' => 'string',
        'sex' => 'integer', // tinyInteger 0|1|2
        'birthday' => 'string',
        'city' => 'string',
        'hobby' => 'string',
        'rating' => 'integer',
        'phone' => 'string',
        'url' => 'string',
        'custom_data' => Serialize::class
    ];

    /**
     * Get user profile via user_id like object (!!! profile.id !== user.id !!!)
     * @param int|null $userId
     * @return self|null
     */
    public static function identity($userId = null)
    {
        if ($userId === null) {
            $userId = MainApp::$Session->get('ff_user_id');
        }

        if ($userId === null || !Any::isInt($userId) || $userId < 1) {
            return null;
        }

        // check in cache
        if (MainApp::$Memory->get('profile.object.cache.' . $userId) !== null) {
            return MainApp::$Memory->get('profile.object.cache.' . $userId);
        }

        // find row
        $profile = self::where('user_id', $userId);

        // empty? lets return null
        if ($profile->count() !== 1) {
            return null;
        }

        $object = $profile->first();
        MainApp::$Memory->set('profile.object.cache.' . $userId, $object);

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
     * Get user full name. If is empty - return 'id+userId'
     * @return string
     */
    public function getName(): ?string
    {
        $userName = $this->name;
        if (!$userName || Str::likeEmpty($userName)) {
            $userName = 'id' . $this->id;
        }

        return $userName;
    }

    /**
     * Get user active record object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
