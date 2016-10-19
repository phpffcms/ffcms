<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

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
 */
class FeedbackPost extends ActiveModel
{
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

    /**
     * Get all answers for this feedback post id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function getAnswers()
    {
        if ($this->id === null) {
            return null;
        }

        return $this->hasMany('Apps\ActiveRecord\FeedbackAnswer', 'feedback_id');
    }

}