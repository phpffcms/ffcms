<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\WallPost as WallRecords;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormWallPost. Add wall post business logic model
 * @package Apps\Model\Front\Profile
 */
class FormWallPost extends Model
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
            ['message', 'length_max', self::MAX_MESSAGE_LENGTH]
        ];
    }

    /**
     * Make post to user wall from $viewer to $target instance of iUser interface objects
     * @param iUser $target
     * @param iUser $viewer
     * @param int $delay
     * @return bool
     */
    public function makePost(iUser $target, iUser $viewer, $delay = 60)
    {
        if ($target === null || $viewer === null) {
            return false;
        }

        $find = WallRecords::where('sender_id', '=', $viewer->id)->orderBy('updated_at', 'desc')->first();
        if ($find !== null) {
            $lastPostTime = Date::convertToTimestamp($find->updated_at);
            if (time() - $lastPostTime < static::POST_GLOBAL_DELAY) { // past time was less then default delay
                return false;
            }
        }

        // save new post to db
        $record = new WallRecords();
        $record->target_id = $target->id;
        $record->sender_id = $viewer->id;
        $record->message = $this->message;
        $record->save();

        // add user notification
        if ($target->id !== $viewer->id) {
            $notify = new EntityAddNotification($target->id);
            $notify->add('profile/show/' . $target->id . '#wall-post-' . $record->id, EntityAddNotification::MSG_ADD_WALLPOST, ['snippet' => Text::snippet($this->message, 50)]);
        }

        // cleanup message
        $this->message = null;

        return true;
    }


}