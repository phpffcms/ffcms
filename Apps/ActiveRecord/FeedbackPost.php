<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Traits\SearchableTrait;
use Illuminate\Support\Collection;

/**
 * Class FeedbackPost. Active model for feedback requests posts.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property bool $readed
 * @property bool $closed
 * @property string $hash
 * @property int $user_id
 * @property string $ip
 * @property string $created_at
 * @property string $updated_at
 * @property FeedbackAnswer[]|Collection $answers
 * @property User|null $user
 */
class FeedbackPost extends ActiveModel
{
    use SearchableTrait;

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'message' => 'string',
        'readed' => 'boolean',
        'closed' => 'boolean',
        'hash' => 'string',
        'user_id' => 'integer',
        'ip' => 'string'
    ];

    protected $searchable = [
        'columns' => [
            'message' => 3
        ]
    ];

    /**
     * Get feedback answers relation object
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(FeedbackAnswer::class, 'feedback_id');
    }

    /**
     * Get user object relation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
