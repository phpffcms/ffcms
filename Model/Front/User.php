<?php


namespace Model\Front;

use Core\App;
use Core\Arch\Model;


class User extends Model
{
    public $name;
    public $role;
    public $email;
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
        if (App::$Request->post('submit') && $this->validateRules()) {
            echo "VALIDATED";
        }
    }

    public function test()
    {
        //$query = \Model\ActiveRecord\Test::find(1);
        //var_dump($query->text);
    }
}