<?php

namespace Apps\Model\Admin\Content;

use Ffcms\Core\Arch\Model;

class FormContentPublish extends Model
{
    /** @var \Apps\ActiveRecord\Content */
    private $_records;

    /**
     * FormContentPublish constructor. Pass records inside
     * @param $records
     */
    public function __construct($records)
    {
        $this->_records = $records;
        parent::__construct();
    }

    /**
     * Update records and set display = 1
     */
    public function make()
    {
        $this->_records->update(['display' => 1]);
    }
}