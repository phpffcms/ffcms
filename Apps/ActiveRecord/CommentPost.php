<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;

/**
 * Class CommentPost. Active record model for comment posts.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $pathway
 * @property int $user_id
 * @property string|null $guest_name
 * @property string $message
 * @property string $lang
 * @property boolean $moderate
 * @property string $created_at
 * @property string $updated_at
 */
class CommentPost extends ActiveModel
{
    /**
     * Get user identity
     * @return User|null
     */
    public function getUser()
    {
        return User::identity($this->user_id);
    }

    /**
     * Get comment post->answers relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAnswer()
    {
        return $this->hasMany('Apps\\ActiveRecord\\CommentAnswer', 'comment_id');
    }

    /**
     * Get answers count for post comment id
     * @return int
     */
    public function getAnswerCount()
    {
        // check if count is cached
        if (MainApp::$Memory->get('commentpost.answer.count.' . $this->id) !== null) {
            return MainApp::$Memory->get('commentpost.answer.count.' . $this->id);
        }
        // get count from db
        $count = CommentAnswer::where('comment_id', '=', $this->id)
            ->where('moderate', '=', 0)
            ->count();
        // save in cache
        MainApp::$Memory->set('commentpost.answer.count.' . $this->id, $count);
        return $count;
    }

}