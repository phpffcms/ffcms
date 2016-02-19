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
 * @property int $is_admin
 * @property int $user_id
 * @property string $ip
 * @property string $created_at
 * @property string $updated_at
 */
class FeedbackAnswer extends ActiveModel
{
    /**
     * Get post relation
     * @return ActiveModel|null
     */
    public function getFeedbackPost()
    {
        return FeedbackPost::find($this->feedback_id);
    }
}