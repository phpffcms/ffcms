<?php

namespace Apps\Model\Admin\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\Arch\Model;

/**
 * Class FormUpdate. Update feedback post or answer business logic model
 * @package Apps\Model\Admin\Feedback
 */
class FormUpdate extends Model
{
    public $name;
    public $email;
    public $message;

    /** @var FeedbackAnswer|FeedbackPost */
    private $_record;

    /**
     * FormUpdate constructor. Pass active record inside the model.
     * @param FeedbackPost|FeedbackAnswer $record
     */
    public function __construct($record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
     * Set model properties from active record data
     */
    public function before()
    {
        $this->name = $this->_record->name;
        $this->email = $this->_record->email;
        $this->message = $this->_record->message;
    }

    /**
     * Labels to display edit form
     * @return array
     */
    public function labels()
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'message' => __('Message'),
        ];
    }

    /**
     * Rules to validate changes
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            ['name', 'length_min', '2'],
            ['message', 'length_min', 10],
            ['email', 'email']
        ];
    }

    /**
     * Save data to database
     */
    public function make()
    {
        $this->_record->name = $this->name;
        $this->_record->email = $this->email;
        $this->_record->message = $this->message;
        $this->_record->save();
    }
}