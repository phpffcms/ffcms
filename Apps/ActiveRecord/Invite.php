<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Date;

/**
 * Class Invite. Active record model to store invite keys
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $token
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 */
class Invite extends ActiveModel
{
    const TOKEN_VALID_TIME = 604800; // 7 days

    protected $casts = [
        'id' => 'integer',
        'token' => 'string',
        'email' => 'string'
    ];

    /**
     * Cleanup old invites
     */
    public static function clean()
    {
        $date = time() - self::TOKEN_VALID_TIME;
        $timestamp = Date::convertToDatetime($date, Date::FORMAT_SQL_TIMESTAMP);
        self::where('created_at', '<=', $timestamp)->delete();
    }

}