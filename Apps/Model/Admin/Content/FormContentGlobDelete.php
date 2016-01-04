<?php

namespace Apps\Model\Admin\Content;


use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Serialize;

class FormContentGlobDelete extends Model
{
    private $_records;

    public $data = [];

    public function __construct($records)
    {
        $this->_records = $records;
        parent::__construct();
    }

    public function before()
    {
        // set data to display in view
        foreach ($this->_records as $row) {
            $this->data[] = [
                'id' => $row->id,
                'title' => Serialize::getDecodeLocale($row->title),
                'date' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_HOUR)
            ];
        }
    }

    public function make()
    {
        foreach ($this->_records as $record) {
            $record->delete();
        }
    }


}