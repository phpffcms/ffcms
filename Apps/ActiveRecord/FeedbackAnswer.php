<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Traits\SearchableTrait;
use Illuminate\Support\Collection;

/**
 * Class FeedbackAnswer. Answers relation to feedback request posts.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $feedback_id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property bool $is_admin
 * @property int $user_id
 * @property string $ip
 * @property string $created_at
 * @property string $updated_at
 * @property FeedbackPost|Collection $post
 */
class FeedbackAnswer extends ActiveModel
{
    use SearchableTrait;

    protected $casts = [
        'id' => 'integer',
        'feedback_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'message' => 'string',
        'is_admin' => 'boolean',
        'user_id' => 'integer',
        'ip' => 'string'
    ];

    protected $searchable = [
        'columns' => [
            'message' => 2
        ]
    ];

    /**
     * Get feedback post relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|FeedbackPost
     */
    public function post()
    {
        return $this->hasOne(FeedbackPost::class, 'id', 'feedback_id');
    }
}
