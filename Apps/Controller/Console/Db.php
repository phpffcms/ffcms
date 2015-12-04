<?php

namespace Apps\Controller\Console;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Ffcms\Console\App;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Security;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;

class Db
{
    public function actionImport($activeRecord)
    {
        $importFile = root . '/Private/Database/Tables/' . ucfirst(strtolower($activeRecord)) . '.php';
        if (!File::exist($importFile)) {
            return App::$Output->write('Database model table not founded: ' . $activeRecord);
        }

        @include($importFile);
        return App::$Output->write('Database table import runned: ' . $activeRecord);
    }

    public function actionImportAll($connectName = 'default')
    {
        $importFile = root . '/Private/Database/install.php';
        if (!File::exist($importFile)) {
            return App::$Output->write('Import file is not exist: ' . $importFile);
        }
        @include($importFile);
        return App::$Output->write('All database tables was imported!');
    }

    public function actionAdduser()
    {
        echo "Login:";
        $login = App::$Input->read();
        if (Str::length($login) < 2) {
            throw new NativeException('Login is bad');
        }
        echo "Email:";
        $email = App::$Input->read();
        if (!Str::isEmail($email)) {
            throw new NativeException('Email is bad');
        }
        echo "Password:";
        $pass = App::$Input->read();
        if (Str::length($pass) < 2) {
            throw new NativeException('Password is bad');
        }
        echo "RoleId (1 = user, 3 = admin):";
        $role = (int)App::$Input->read();
        if (!Arr::in($role, [1,2,3])) {
            $role = 1;
        }

        if (User::isMailExist($email) || User::isLoginExist($login)) {
            throw new NativeException('User with this email or login is always exist');
        }

        $salt = App::$Properties->get('passwordSalt');

        $user = new User();
        $user->login = $login;
        $user->email = $email;
        $user->password = Security::password_hash($pass, $salt);
        $user->role_id = $role;
        $user->save();

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->save();

        return App::$Output->write('User was successful added to database!');
    }


}