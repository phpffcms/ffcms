<?php

namespace Apps\Model\Basic;

use Apps\Model\ActiveRecord\User as ARecordUser;
use Ffcms\Core\Interfaces\iUser;

class User extends ARecordUser implements iUser
{
    protected $cached;

    public function getId()
    {
        if (!$this->isAuth()) {
            return 0;
        }
        return $this->get('id');
    }

    public function get($param, $custom_id = null)
    {
        // TODO: Implement get() method.
    }

    public function isAuth()
    {
        // TODO: Implement isAuth() method.
    }

    public function isExist($id)
    {
        // TODO: Implement isExist() method.
    }

    public function getSession($custom_id = null)
    {
        // TODO: Implement getSession() method.
    }

    public function isMailExist($email)
    {
        // TODO: Implement isMailExist() method.
    }

    public function isLoginExist($login)
    {
        // TODO: Implement isLoginExist() method.
    }

    public function getPersonViaEmail($email)
    {
        // TODO: Implement getPersonViaEmail() method.
    }
}