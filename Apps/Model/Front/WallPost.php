<?php

namespace Apps\Model\Front;

use Apps\ActiveRecord\Wall;
use Ffcms\Core\App;
use Ffcms\Core\Interfaces\iUser;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;

class WallPost extends Model
{
    const MAX_MESSAGE_LENGTH = 500; // 500 symbols
    const POST_GLOBAL_DELAY = 30; // delay between 2 posts from 1 user in seconds

    public $message;

    /**
     * Validate rules for message field
     * @return array
     */
    public function rules()
    {
        return [
            ['message', 'required'],
            ['message', 'length_min', 5],
            ['message', 'length_max', static::MAX_MESSAGE_LENGTH]
        ];
    }

    /**
     * Make post to user wall from $viewer to $target instance of iUser interface objects
     * @param iUser $target
     * @param iUser $viewer
     * @return bool
     */
    public function makePost(iUser $target, iUser $viewer)
    {
        if ($target === null || $viewer === null) {
            return false;
        }

        $find = Wall::where('sender_id', '=', $viewer->id)->orderBy('updated_at', 'desc')->first();
        if ($find !== null) {
            $lastPostTime = Date::convertToTimestamp($find->updated_at);
            if (time() - $lastPostTime < static::POST_GLOBAL_DELAY) { // past time was less then default delay
                return false;
            }
        }

        // save new post to db
        $record = new Wall();
        $record->target_id = $target->id;
        $record->sender_id = $viewer->id;
        $record->message = App::$Security->strip_tags($this->message);
        $record->save();

        // cleanup message
        $this->message = null;

        return true;
    }


}