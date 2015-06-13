<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Date;

class Invite extends ActiveModel
{

    const TOKEN_VALID_TIME = 604800; // 7 days

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