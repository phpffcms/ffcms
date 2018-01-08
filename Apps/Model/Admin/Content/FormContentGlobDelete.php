<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class FormContentGlobDelete. Mass delete content items from passed active record collection
 * @package Apps\Model\Admin\Content
 */
class FormContentGlobDelete extends Model
{
    /** @var Content[]|Collection */
    private $_records;

    public $data = [];

    /**
     * FormContentGlobDelete constructor. Pass records inside.
     * @param Content[]|Collection $records
     */
    public function __construct($records)
    {
        $this->_records = $records;
        parent::__construct();
    }

    /**
     * Construct records into data array to display in view
     */
    public function before()
    {
        // set data to display in view
        foreach ($this->_records as $row) {
            $this->data[] = [
                'id' => $row->id,
                'title' => $row->getLocaled('title'),
                'date' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_HOUR)
            ];
        }
    }

    /**
     * Delete founded records
     */
    public function make()
    {
        foreach ($this->_records as $record) {
            $record->delete();
        }
    }
}
