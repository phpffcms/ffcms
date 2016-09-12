<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\Directory;
use Illuminate\Database\Eloquent\Collection;

class FormContentClear extends Model
{
    public $count = 0;

    /** @var Content|Collection */
    private $_records;

    /**
     * FormContentClear constructor. Pass content records collection object inside (can be empty collection)
     * @param Content|Collection $records
     */
    public function __construct($records)
    {
        $this->_records = $records;
        $this->count = $this->_records->count();
        parent::__construct();
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels()
    {
        return [
            'count' => __('Trashed content')
        ];
    }

    /**
     * Finally delete content item
     */
    public function make()
    {
        // remove gallery files if exists
        foreach ($this->_records->get() as $record) {
            $galleryPath = '/upload/gallery/' . (int)$record->id;
            if (Directory::exist($galleryPath)) {
                Directory::remove($galleryPath);
            }
        }

        // finally remove from db
        $this->_records->forceDelete();
    }
}