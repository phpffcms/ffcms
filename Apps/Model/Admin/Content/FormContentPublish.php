<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\Arch\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class FormContentPublish. Make content record public - set marker in active record object
 * @package Apps\Model\Admin\Content
 */
class FormContentPublish extends Model
{
    /** @var Content|Collection */
    private $_records;

    /**
     * FormContentPublish constructor. Pass records inside
     * @param Content[]|Collection $records
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
