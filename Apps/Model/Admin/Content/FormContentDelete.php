<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

class FormContentDelete extends Model
{
    public $id;
    public $title;

    private $_record;

    /**
     * Pass object in model on init
     * @param Content $record
     */
    public function __construct(Content $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
    * Set readable title of content to property
    */
    public function before()
    {
        $this->id = $this->_record->id;
        $this->title = Serialize::getDecodeLocale($this->_record->title);
    }

    /**
    * Form labels
    */
    public function labels()
    {
        return [
            'title' => __('Title')
        ];
    }

    /**
    * Validation does not matter
    */
    public function rules()
    {
        return [
            ['id', 'used']
        ];
    }

    /**
     * Make delete
     * @throws \Exception
     */
    public function make()
    {
        $this->_record->delete();
    }
}