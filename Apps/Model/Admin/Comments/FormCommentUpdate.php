<?php

namespace Apps\Model\Admin\Comments;

use Ffcms\Core\Arch\Model;

/**
 * Class FormCommentUpdate. Model for display & update comments and answers
 * @package Apps\Model\Admin\Comments
 */
class FormCommentUpdate extends Model
{
    public $message;
    public $guestName;

    /** @var \Apps\ActiveRecord\CommentPost|\Apps\ActiveRecord\CommentAnswer $_record */
    private $_record;
    private $type;

    /**
     * FormCommentUpdate constructor. Pass record inside the model.
     * @param \Apps\ActiveRecord\CommentPost|\Apps\ActiveRecord\CommentAnswer $record
     * @param string $type
     */
    public function __construct($record, $type = 'comment')
    {
        $this->_record = $record;
        $this->type = $type;
        parent::__construct();
    }

    /**
     * Set default values from active record data
     */
    public function before()
    {
        $this->message = $this->_record->message;
        $this->guestName = $this->_record->guest_name;
    }

    /**
     * Labels to display in view
     */
    public function labels(): array
    {
        return [
            'message' => __('Message'),
            'guestName' => __('Guest name')
        ];
    }

    /**
     * Validation rules for comment body
     */
    public function rules(): array
    {
        return [
            ['message', 'required'],
            ['guestName', 'used'],
            ['guestName', 'length_max', 100]
        ];
    }

    /**
     * Set attribute validation types
     * @return array
     */
    public function types(): array
    {
        return [
            'message' => 'html'
        ];
    }

    /**
     * Save updated data to database
     */
    public function make()
    {
        $this->_record->message = $this->message;
        $this->_record->guest_name = $this->guestName;
        $this->_record->save();
    }

    /**
     * Get comment_id for comment or answer
     * @return int
     */
    public function getCommentId(): int
    {
        $id = $this->_record->id;
        if ($this->type === 'answer') {
            $id = $this->_record->post->id;
        }

        return $id;
    }
}
