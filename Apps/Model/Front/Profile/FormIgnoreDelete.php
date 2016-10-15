<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormIgnoreDelete. Business logic of delete ignored user for current users object from database
 * @package Apps\Model\Front\Profile
 */
class FormIgnoreDelete extends Model
{
    public $id;

    private $_user;
    private $_target_id;

    /**
     * FormIgnoreDelete constructor. Pass current user object and target user id inside
     * @param iUser $user
     * @param int $target_id
     */
    public function __construct(iUser $user, $target_id)
    {
        $this->_user = $user;
        $this->_target_id = $target_id;
        parent::__construct(true);
    }

    /**
     * Set public display data
     */
    public function before()
    {
        $this->id = $this->_target_id;
    }

    /**
     * Display labels in form
     * @return array
     */
    public function labels()
    {
        return [
            'id' => __('User ID'),
            'name' => __('Nickname')
        ];
    }

    /**
     * Form submit action - delete user from database
     * @throws \Exception
     */
    public function make()
    {
        Blacklist::where('user_id', '=', $this->_user->getId())
            ->where('target_id', '=', $this->_target_id)
            ->delete();
    }
}