<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

/**
 * Class FormContentDelete. Delete content item business logic
 * @package Apps\Model\Admin\Content
 */
class FormContentDelete extends Model
{
    public $id;
    public $title;

    private $_record;

    /**
     * FormContentDelete constructor. Pass object in model on init
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
     * Make delete
     * @throws \Exception
     */
    public function make()
    {
        $this->_record->getTags()->delete();
        $this->_record->getRating()->delete();
        $this->_record->delete();
    }
}