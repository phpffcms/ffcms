<?php

namespace Apps\ActiveRecord;


use Ffcms\Core\Arch\ActiveModel;

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
 */
class FeedbackAnswer extends ActiveModel
{
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

    /**
     * Get post relation
     * @return FeedbackPost|null
     */
    public function getFeedbackPost()
    {
        return FeedbackPost::find($this->feedback_id);
    }
}