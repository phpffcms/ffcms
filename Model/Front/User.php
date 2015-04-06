<?php


namespace Model\Front;

use \Ffcms\Core\Arch\Model;

class User extends Model
{
    public $name;
    public $role;
    public $isJoined = false;

    public function setLabels()
    {
        return [
            'name' => 'User name',
            'role' => 'User role'
        ];
    }

    public function setRules()
    {
        return [
            ['name', 'length_min', '1'],
            ['name', 'length_max', '12'],
        ];
    }

    public function make()
    {
        if($this->validateRules()) {

        }
    }
}