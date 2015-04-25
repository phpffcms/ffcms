<?php

namespace Controller\Front;

use Core\Arch\Controller;
use Core\App;
use Core\Arch\View;
use Core\Exception\ErrorException;

class User extends Controller
{

    /**
     * View login form
     */
    public function actionLogin()
    {
        if (App::$User->isAuth()) {
            return new ErrorException('You are always log in');
        }
        $this->response = App::$View->render('login', [

        ]);
        //$this->response = (new View('test', 'fuck'))->out([
        //    'a' => 'b'
        //]);
        //$this->response = 'Welcome to login form';
    }
}