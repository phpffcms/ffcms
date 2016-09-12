<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

/**
 * Class FormContentRestore. Restore deleted content item from active record trash state
 * @package Apps\Model\Admin\Content
 */
class FormContentRestore extends Model
{
    public $id;
    public $title;

    private $_record;

    /**
     * FormContentRestore constructor. Pass active record object inside
     * @param Content $record
     */
    public function __construct(Content $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
     * Set public attributes from active record object
     */
    public function before()
    {
        $this->id = $this->_record->id;
        $this->title = Serialize::getDecodeLocale($this->_record->title);
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels()
    {
        return [
            'title' => __('Title')
        ];
    }

    /**
     * Make restore of trashed item
     */
    public function make()
    {
        $this->_record->restore();
    }
}