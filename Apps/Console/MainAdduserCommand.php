<?php

namespace Apps\Console;


use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\User;
use Ffcms\Console\Command;
use Ffcms\Console\Console;
use Ffcms\Core\Helper\Security;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MainAdduserCommand. Add new user in database
 * @package Apps\Console
 */
class MainAdduserCommand extends Command
{
    /**
     * Register adduser command
     */
    public function configure()
    {
        $this->setName('main:adduser')
            ->setDescription('Add new user into database')
            ->addOption('login', 'login', InputOption::VALUE_OPTIONAL, 'Set user login. Should be unique!')
            ->addOption('email', 'email', InputOption::VALUE_OPTIONAL, 'Set user email. Should be unique!')
            ->addOption('password', 'password', InputOption::VALUE_OPTIONAL, 'Set user password')
            ->addOption('role', 'role', InputOption::VALUE_OPTIONAL, 'Define user role_id. Should be integer (see prefix_roles table). By default: 1=guest, 2=user, 3=moder, 4=admin');
    }

    /**
     * Add new user in database by passed params
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get login and check validity
        $login = $this->optionOrAsk('login', 'User login');
        if (Str::length($login) < 2) {
            throw new \Exception('Login is too short');
        }
        // get email and check validity
        $email = $this->optionOrAsk('email', 'User email');
        if (!Str::isEmail($email)) {
            throw new \Exception('Email syntax is wrong');
        }
        // get password and role and check validity
        $password = $this->optionOrAsk('password', 'User password');
        $roleId = (int)$this->optionOrAsk('role', 'RoleId', '1');
        $records = Role::all()->toArray();
        $roles = Arr::pluck('id', $records);
        if (!Arr::in($roleId, $roles)) {
            throw new \Exception('RoleId is not found');
        }

        // check if user is always exists
        if (User::isLoginExist($login) || User::isMailExist($email)) {
            $output->writeln('User is always exists');
            return;
        }

        // create new user instance in prefix_users table
        $salt = Console::$Properties->get('passwordSalt');
        $user = new User();
        $user->login = $login;
        $user->email = $email;
        $user->password = Security::password_hash($password, $salt);
        $user->role_id = $roleId;
        $user->save();

        // crate empty user profile in prefix_profiles table
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->save();
        $output->writeln('New user are successful added');
    }
}