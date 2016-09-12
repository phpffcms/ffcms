<?php

namespace Apps\Model\Admin\Application;

use Apps\ActiveRecord\App;
use Ffcms\Core\Arch\Model;

/**
 * Class FormTurn. Turn on/off model for application object
 * @package Apps\Model\Admin\Application
 */
class FormTurn extends Model
{
    private $_record;

    /**
     * FormTurn constructor. Pass app object inside.
     * @param App $record
     */
    public function __construct(App $record)
    {
        $this->_record = $record;
        parent::__construct(true);
    }

    /**
     * Switch app status to inverse
     */
    public function update()
    {
        $status = $this->_record->disabled;

        $this->_record->disabled = (int)!$status; // magic inside: bool to int and reverse - 0 => 1, 1 => 0
        $this->_record->save();
    }
}