<?php

namespace Apps\Model\Admin\Feedback;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;

class FormUpdate extends Model
{
    public $name;
    public $email;
    public $message;

    private $_record;

    /**
     * FormUpdate constructor. Pass active record inside the model.
     * @param $record
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
        $this->_record->name = App::$Security->strip_tags($this->name);
        $this->_record->email = App::$Security->strip_tags($this->email);
        $this->_record->message = App::$Security->strip_tags($this->message);
        $this->_record->save();
    }
}