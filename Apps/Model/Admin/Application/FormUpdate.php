<?php

namespace Apps\Model\Admin\Application;

use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;

class FormUpdate extends Model
{
    public $name;
    public $dbVersion;
    public $scriptVersion;
    public $date;

    /** @var \Apps\ActiveRecord\App $_record */
    private $_record;
    /** @var string $_callback */
    private $_callback;

    public function __construct(AppRecord $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
    * Magic method before
    */
    public function before()
    {
        // get full name of update object
        $class = 'Apps\Controller\Admin\\' . $this->_record->sys_name;
        if (class_exists($class)) {
            $this->_callback = $class;
        } else {
            throw new NotFoundException(__('Admin controller is not founded - %c%', ['c' => $this->_record->sys_name]));
        }

        // compare versions
        if ($this->_record->checkVersion() === true) {
            throw new ForbiddenException('Extension is not be updated - version comparing done successful');
        }

        // set public attributes to display
        $this->name = $this->_record->getLocaleName();
        $this->dbVersion = $this->_record->version;
        $this->scriptVersion = $this->_record->getScriptVersion();
        $this->date = Date::convertToDatetime($this->_record->updated_at, Date::FORMAT_TO_HOUR);
    }

    /**
     * Make update actions
     */
    public function make()
    {
        // make query to ClassController::update(version)
        @forward_static_call_array([$this->_callback, 'update'], [$this->_record->version]);
        // update version in db
        $this->_record->version = $this->_record->getScriptVersion();
        $this->_record->save();
    }
}