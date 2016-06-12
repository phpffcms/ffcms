<?php

namespace Apps\Controller\Console;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Ffcms\Console\Console;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Security;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Db. Working with database data
 * @package Apps\Controller\Console
 */
class Db
{
    /**
     * Import table with model name $activeRecord to database
     * @param string $activeRecord
     * @return string
     */
    public function actionImport($activeRecord)
    {
        $importFile = root . '/Private/Database/Tables/' . ucfirst(strtolower($activeRecord)) . '.php';
        if (!File::exist($importFile)) {
            return 'Database model table not founded: ' . $activeRecord;
        }

        @include($importFile);
        return 'Database table import done: ' . $activeRecord;
    }

    /**
     * Import all tables to database
     * @param string $connectName
     * @return string
     */
    public function actionImportAll($connectName = 'default')
    {
        $importFile = root . '/Private/Database/install.php';
        if (!File::exist($importFile)) {
            return 'Import file is not exist: ' . $importFile;
        }
        @include($importFile);
        return 'All database tables was imported!';
    }

    /**
     * Add user in database
     * @return string
     * @throws NativeException
     */
    public function actionAdduser()
    {
        echo "Login:";
        $login = Console::$Input->read();
        if (Str::length($login) < 2) {
            throw new NativeException('Login is bad');
        }
        echo "Email:";
        $email = Console::$Input->read();
        if (!Str::isEmail($email)) {
            throw new NativeException('Email is bad');
        }
        echo "Password:";
        $pass = Console::$Input->read();
        if (Str::length($pass) < 2) {
            throw new NativeException('Password is bad');
        }
        echo "RoleId (1 = onlyread, 2 = user, 3 = moderator, 4 = admin):";
        $role = (int)Console::$Input->read();
        if (!Arr::in($role, [1,2,3,4])) {
            $role = 2;
        }

        if (User::isMailExist($email) || User::isLoginExist($login)) {
            throw new NativeException('User with this email or login is always exist');
        }

        $salt = Console::$Properties->get('passwordSalt');

        $user = new User();
        $user->login = $login;
        $user->email = $email;
        $user->password = Security::password_hash($pass, $salt);
        $user->role_id = $role;
        $user->save();

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->save();

        return 'User was successful added to database!';
    }
}