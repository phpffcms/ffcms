<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Traits\SearchableTrait;

/**
 * Class CommentPost. Active record model for comment posts.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $app_name
 * @property int $app_relation_id
 * @property int $user_id
 * @property string|null $guest_name
 * @property string $message
 * @property string $lang
 * @property boolean $moderate
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property CommentAnswer[] $answers
 */
class CommentPost extends ActiveModel
{
    use SearchableTrait;

    protected $casts = [
        'id' => 'integer',
        'app_name' => 'string',
        'app_relation_id' => 'integer',
        'user_id' => 'integer',
        'guest_name' => 'string',
        'message' => 'string',
        'lang' => 'string',
        'moderate' => 'boolean'
    ];

    protected $searchable = [
        'columns' => [
            'message' => 1
        ]
    ];

    /**
     * Get user relation object
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get answers relation objects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(CommentAnswer::class, 'comment_id');
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
        $count = CommentAnswer::where('comment_id', $this->id)
            ->where('moderate', 0)
            ->count();
        // save in cache
        MainApp::$Memory->set('commentpost.answer.count.' . $this->id, $count);
        return $count;
    }
}
