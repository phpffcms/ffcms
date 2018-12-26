<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormIgnoreAdd. Business logic of add users to ignore list in database for current user.
 * @package Apps\Model\Front\Profile
 */
class FormIgnoreAdd extends Model
{
    public $id;
    public $comment;

    private $_user;

    /**
     * FormIgnoreAdd constructor. Pass user object inside.
     * @param iUser $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct(true);
    }

    /**
     * Labels for form
     * @return array
     */
    public function labels(): array
    {
        return [
            'id' => __('User ID'),
            'comment' => __('Comment')
        ];
    }

    /**
     * Validation rules
     */
    public function rules(): array
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
        $record->comment = $this->comment;
        $record->save();

        return true;
    }
}
