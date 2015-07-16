<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

class FormIgnoreDelete extends Model
{
    public $id;
    public $name;

    private $_user;
    private $_target_id;

    public function __construct(iUser $user, $target_id)
    {
        $this->_user = $user;
        $this->_target_id = $target_id;
        parent::__construct();
    }

    public function before()
    {
        $this->id = $this->_target_id;
        $this->name = App::$User->identity($this->_target_id)->getProfile()->nick;
    }

    public function labels()
    {
        return [
            'id' => __('User ID'),
            'name' => __('Nickname')
        ];
    }

    public function make()
    {
        Blacklist::where('user_id', '=', $this->_user->getId())
            ->where('target_id', '=', $this->_target_id)
            ->delete();
    }
}