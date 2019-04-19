<?php


namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

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

        return ($this->timestamp &&
            (($now - $this->timestamp)/60 < static::ACTIVITY_COUNT_LIFETIME) &&
            $this->counter > static::ACTIVITY_COUNT_THRESHOLD);
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

        // check if timestamp exist & passed low then threshold limit
        if ($row->timestamp && $row->timestamp > 0) {
            if (($now - $row->timestamp)/60 < static::ACTIVITY_COUNT_LIFETIME) {
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


}