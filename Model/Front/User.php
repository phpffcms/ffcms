<?php


namespace Model\Front;

use Core\App;
use \Core\Arch\Model;
use \Model\ActiveRecord\Test;


class User extends Model
{
    public $name;
    public $role;
    public $isJoined = true;

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
            ['isJoined', 'boolean'],
            ['name', 'Core\Security::password_hash'],
            ['role', 'in', ['user', 'admin']]
        ];
    }

    public function make()
    {
        if (App::$Request->post('submit') && $this->validateRules()) {

        }
    }

    public function test()
    {
        $query = Test::find(1);
        //var_dump($query->text);
    }
}