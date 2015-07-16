<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

class FormIgnoreAdd extends Model
{
    public $id;
    public $comment;

    private $_user;

    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct();
    }

    /**
     * Labels for form
     * @return array
     */
    public function labels()
    {
        return [
            'id' => __('User ID'),
            'comment' => __('Comment')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            ['id', 'required'],
            ['comment', 'used'],
            ['id', 'int'],
            ['id', 'App::$User::isExist']
        ];
    }

    /**
     * Make save
     * @return bool
     */
    public function save()
    {
        // check if target is myself or always exist in block list
        if ($this->_user->getId() === (int)$this->id || Blacklist::have($this->_user->getId(), $this->id)) {
            return false;
        }

        // save data to db
        $record = new Blacklist();
        $record->user_id = $this->_user->getId();
        $record->target_id = $this->id;
        $record->comment = App::$Security->strip_tags($this->comment);
        $record->save();

        return true;
    }
}