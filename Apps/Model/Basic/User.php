<?php

namespace Apps\Model\Basic;

use Apps\Model\ActiveRecord\User as ARecordUser;
use Extend\Core\App;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Interfaces\iUser;

class User extends ARecordUser implements iUser
{

    /**
     * Get current user id if auth
     * @return int|null
     */
    public function getId()
    {
        if (!$this->isAuth()) {
            return null;
        }
        return $this->get('id');
    }

    /**
     * Get user param
     * @param string $param
     * @param null|int $custom_id
     * @return mixed|null
     */
    public function get($param, $custom_id = null)
    {
        $object = $this->getPerson($custom_id);
        if (false === $object) {
            return null;
        }

        return $object->param;
    }

    /**
     * Get user person all data like a object
     * @param null|int $custom_id
     * @return bool|\Illuminate\Support\Collection|null|static
     */
    public function getPerson($custom_id = null)
    {
        if (null === $custom_id) {
            if (!$this->isAuth()) {
                return false;
            } else {
                return self::find($_SESSION['ff_user_id']);
            }
        } elseif (Object::isInt($custom_id) && $custom_id > 0) {
            return self::find($custom_id);
        }

        return false;
    }

    /**
     * Check if current user session is auth
     * @return bool
     */
    public function isAuth()
    {
        App::$Session->start();
        $session_token = $_SESSION['ff_user_token'];
        $session_id = $_SESSION['ff_user_id'];
        if (null === $session_token || !Object::isInt($session_id) || $session_id < 1 || String::length($session_token) < 64) {
            return false;
        }

        $find = self::find($session_id)
            ->where('token_data', '=', $session_token)
            ->count();
        return $find > 0;
    }

    /**
     * Check if user with $id exist
     * @param int $id
     * @return bool
     */
    public function isExist($id)
    {
        if (!Object::isInt($id) || $id < 1) {
            return false;
        }

        return self::find($id)->count() > 0;
    }

    /**
     * Check if use with $email is exist
     * @param string $email
     * @return bool
     */
    public function isMailExist($email)
    {
        if (!Object::isString($email) || !String::isEmail($email)) {
            return false;
        }

        return self::where('email', '=', $email)->count() > 0;
    }

    /**
     * Check if user with $login is exist
     * @param string $login
     * @return bool
     */
    public function isLoginExist($login)
    {
        if (!Object::isString($login) || String::length($login) < 1) {
            return false;
        }

        return self::where('login', '=', $login)->count() > 0;
    }

    /**
     * Get user person like a object via email
     * @param string $email
     * @return bool
     */
    public function getPersonViaEmail($email)
    {
        if (!$this->isMailExist($email)) {
            return false;
        }

        return self::where('email', '=', $email)->first();
    }
}