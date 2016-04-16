<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Date;

/**
 * Class UserLog. Active record model.
 * @property $id int
 * @property $user_id int
 * @property $type string
 * @property $message string
 * @property $created_at string
 * @property $updated_at string
 */
class UserLog extends ActiveModel
{
    const RAND_CHANCE = 10; // chance in percentage to run cleanup

    /**
     * Cleanup rows oldest then 1 week
     */
    public static function cleanup()
    {
        // run cleanup with chance
        if (mt_rand(0, 100) > static::RAND_CHANCE) {
            return;
        }

        // get session max lifetime
        $lifetime = App::$Session->getMetadataBag()->getLifetime();
        // multiple x2 to prevent any shits ;D
        $lifetime *= 2;
        // current unixtime minus lifetime
        $timestamp = time() - $lifetime;
        $sqlFormatedTime = Date::convertToDatetime($timestamp, Date::FORMAT_SQL_DATE);

        /// remove oldest rows
        self::where('created_at', '<=', $sqlFormatedTime)->delete();
    }

    /**
     * Override save method - cleanup before save
     * {@inheritDoc}
     * @see \Illuminate\Database\Eloquent\Model::save()
     */
    public function save(array $opt = [])
    {
        self::cleanup();
        parent::save($opt);
    }
}