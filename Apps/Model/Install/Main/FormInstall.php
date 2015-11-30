<?php

namespace Apps\Model\Install\Main;

use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\User;
use Apps\Controller\Console\Db;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

class FormInstall extends Model
{
    public $db = [];
    public $email;
    public $multiLanguage;
    public $singleLanguage;
    public $user = [];

    public function before()
    {
        $this->db['charset'] = 'utf8';
        $this->db['collation'] = 'utf8_unicode_ci';
    }

    /**
    * Labels for installation form
    */
    public function labels()
    {
        return [
            'db.driver' => __('Database type'),
            'db.host' => __('Host'),
            'db.username' => __('User name'),
            'db.password' => __('User password'),
            'db.database' => __('Database name'),
            'db.prefix' => __('Table prefix'),
            'email' => __('SendFrom email'),
            'singleLanguage' => __('Default language'),
            'multiLanguage' => __('Multi language'),
            'user.login' => __('Login'),
            'user.email' => __('Email'),
            'user.password' => __('Password')
        ];
    }

    /**
    * Installation post data validation
    */
    public function rules()
    {
        return [
            [['db.driver', 'db.host', 'db.username', 'db.password', 'db.database', 'db.prefix', 'email', 'singleLanguage'], 'required'],
            [['user.login', 'user.email', 'user.password'], 'required'],
            [['user.login', 'user.password'], 'length_min', 4],
            ['user.email', 'email'],
            ['multiLanguage', 'used'],
            ['db.driver', 'in', ['mysql', 'pgsql', 'sqlite']],
            ['email', 'email'],
            ['singleLanguage', 'in', App::$Translate->getAvailableLangs()],
            ['db', 'Apps\Model\Install\Main\FormInstall::filterCheckDb']
        ];
    }

    public function make()
    {
        // prepare configurations to save
        $cfg = App::$Properties->getAll('default');
        $cfg['database'] = $this->db;
        $cfg['adminEmail'] = $this->email;
        $cfg['singleLanguage'] = $this->singleLanguage;
        $cfg['multiLanguage'] = (bool)$this->multiLanguage;
        $cfg['passwordSalt'] = '$2a$07$' . Str::randomLatinNumeric(mt_rand(21, 30)) . '$';
        $cfg['debug']['cookie']['key'] = 'fdebug_' . Str::randomLatinNumeric(mt_rand(4, 16));
        $cfg['debug']['cookie']['value'] = Str::randomLatinNumeric(mt_rand(32, 128));

        // import database tables
        $connectName = 'install';
        include(root . '/Private/Database/install.php');

        // insert admin user
        $user = new User();
        $user->setConnection('install');
        $user->login = $this->user['login'];
        $user->email = $this->user['email'];
        $user->role_id = 3;
        $user->password = App::$Security->password_hash($this->user['password'], $cfg['passwordSalt']);
        $user->save();

        $profile = new Profile();
        $profile->setConnection('install');
        $profile->user_id = $user->id;
        $profile->save();

        // write config data
        App::$Properties->writeConfig('default', $cfg);
        File::write('/Private/Install/install.lock', 'Installation is locked!');
    }


    /**
     * Check database connection filter
     * @param array $cfg
     * @return bool
     */
    public function filterCheckDb($cfg = [])
    {
        App::$Database->addConnection($this->db, 'install');

        try {
            App::$Database->connection('install')->getDatabaseName();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}