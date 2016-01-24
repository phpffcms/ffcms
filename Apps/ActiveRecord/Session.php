<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;

/**
 * Class Session. Session storage table.
 * @package Apps\ActiveRecord
 */
class Session extends ActiveModel
{
    /**
     * Get current active sessions count (authorized users online)
     * @return int
     */
    public static function getOnlineCount()
    {
        if (App::$Memory->get('cache.users.online') !== null) {
            return App::$Memory->get('cache.users.online');
        }

        // get online sessions now
        $time = time() - 600; // current_time - 10 minutes
        $query = self::where('sess_time', '>=', $time);
        $count = $query->count();

        // save to memory
        App::$Memory->set('cache.users.online', $count);

        // return count
        return $count;
    }

}