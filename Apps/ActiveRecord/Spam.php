<?php


namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\App as AppMain;
use Ffcms\Core\Helper\Type\Any;

/**
 * Class Spam. Spam activity control filter
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $ipv4
 * @property int $user_id
 * @property int $timestamp
 * @property int $counter
 * @property string $created_at
 * @property string $updated_at
 */
class Spam extends ActiveModel
{
    const ACTIVITY_COUNT_LIFETIME = 2; // in minutes
    const ACTIVITY_COUNT_THRESHOLD = 5; // threshold count after captcha will shown

    protected $casts = [
        'user_id' => 'integer',
        'timestamp' => 'integer',
        'counter' => 'integer'
    ];

    /**
     * Check if threshold is reached for current row
     * @return bool
     */
    public function isThresholdReached(): bool
    {
        $now = time();

        $settings = AppMain::$Properties->get('captcha');
        $lifetime = static::ACTIVITY_COUNT_LIFETIME;
        $count = static::ACTIVITY_COUNT_THRESHOLD;
        if ($settings && $settings['time'] && Any::isInt($settings['time'])) {
            $lifetime = (int)$settings['time'];
        }
        if ($settings && $settings['threshold'] && Any::isInt($settings['threshold'])) {
            $count = (int)$settings['threshold'];
        }

        return ($this->timestamp &&
            (($now - $this->timestamp)/60 < $lifetime) &&
            $this->counter > $count);
    }

    /**
     * Save user activity in database and update counter
     * @param string $ipv4
     * @param int|null $userId
     * @return Spam
     */
    public static function activity(string $ipv4, ?int $userId = null): Spam
    {
        // find ip/user_id row in database
        $query = self::where('ipv4', $ipv4);
        if ($userId) {
            $query = $query->where('user_id', $userId);
        }
        // now timestamp
        $now = time();

        // check if record exist or create it
        $row = $query->first();
        if (!$row) {
            $row = new self();
            $row->ipv4 = $ipv4;
            if ($userId) {
                $row->user_id = $userId;
            }
        }

        $settings = AppMain::$Properties->get('captcha');
        $lifetime = static::ACTIVITY_COUNT_LIFETIME;
        if ($settings && $settings['time'] && Any::isInt($settings['time'])) {
            $lifetime = (int)$settings['time'];
        }

        // check if timestamp exist & passed low then threshold limit
        if ($row->timestamp && $row->timestamp > 0) {
            if (($now - $row->timestamp)/60 < $lifetime) {
                $row->counter += 1;
            } else {
                // drop counter if lifetime gone away
                $row->counter = 0;
            }
        }

        // update timestamp and save record
        $row->timestamp = $now;
        $row->save();

        return $row;
    }

    /**
     * Check if captcha required without threshold increment
     * @param string $ipv4
     * @param int|null $userId
     * @return bool
     */
    public static function check(string $ipv4, ?int $userId = null): bool
    {
        $settings = AppMain::$Properties->get('captcha');
        $lifetime = static::ACTIVITY_COUNT_LIFETIME;
        $count = static::ACTIVITY_COUNT_THRESHOLD;
        if ($settings && $settings['time'] && Any::isInt($settings['time'])) {
            $lifetime = (int)$settings['time'];
        }
        if ($settings && $settings['threshold'] && Any::isInt($settings['threshold'])) {
            $count = (int)$settings['threshold'];
        }

        $query = self::where('ipv4', $ipv4);
        if ($userId) {
            $query = $query->where('user_id', $userId);
        }

        if ($query->count() < 1) {
            return true;
        }

        // now timestamp
        $now = time();
        /** @var self $row */
        $row = $query->first();

        if (!$row || !$row->timestamp || $row->timestamp < 1) {
            return true;
        }

        if (($now - $row->timestamp)/60 > $lifetime) {
            return true;
        }

        return $row->counter <= $count;
    }


}