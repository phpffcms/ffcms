<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

class FormContentRestore extends Model
{
    public $id;
    public $title;

    private $_record;

    public function __construct(Content $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
    * Pass properties
    */
    public function before()
    {
        $this->id = $this->_record->id;
        $this->title = Serialize::getDecodeLocale($this->_record->title);
    }

    /**
    * Form label
    */
    public function labels()
    {
        return [
            'title' => __('Title')
        ];
    }

    /**
    * Typical rules
    */
    public function rules()
    {
        return [];
    }

    /**
     * Make restore of trashed item
     */
    public function make()
    {
        $this->_record->restore();
    }
}