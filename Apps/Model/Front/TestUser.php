<?php


namespace Apps\Model\Front;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;


class User extends Model
{
    public $name;
    public $role;
    public $email;
    public $isJoined = true;

    public function labels()
    {
        return [
            'name' => 'User name',
            'role' => 'User role'
        ];
    }

    public function rules()
    {
        return [
            [['name', 'role', 'email'], 'required'],
            ['name', 'length_min', '1'],
            ['name', 'length_max', '12'],
            ['isJoined', 'boolean'],
            ['email', 'email'],
            //['name', 'Core\Helper\String::contains'],
            ['role', 'in', ['user', 'admin']]
        ];
    }

    public function make()
    {
        if (App::$Request->get('submit', false) && $this->validate()) {
            echo "VALIDATED";
        }
    }
}