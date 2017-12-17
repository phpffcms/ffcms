<?php

namespace Apps\Model\Install\Main;

use Apps\ActiveRecord\Migration;
use Apps\ActiveRecord\Profile;
use Apps\ActiveRecord\System;
use Apps\ActiveRecord\User;
use Extend\Version;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Security;
use Ffcms\Core\Managers\MigrationsManager;

/**
 * Class FormInstall. System installation business logic model
 * @package Apps\Model\Install\Main
 */
class FormInstall extends Model
{
    public $db = [];
    public $email;
    public $multiLanguage;
    public $singleLanguage;
    public $user = [];
    public $mainpage;

    public $baseDomain;

    /**
     * Set default data
     */
    public function before()
    {
        $this->db['charset'] = 'utf8';
        $this->db['collation'] = 'utf8_unicode_ci';

        $this->baseDomain = App::$Request->getHttpHost();
    }

    /**
     * Labels for installation form
     * @return array
     */
    public function labels(): array
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
            'user.password' => __('Password'),
            'user.repassword' => __('Repeat password'),
            'mainpage' => __('Main page')
        ];
    }

    /**
     * Installation post data validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['db.driver', 'db.host', 'db.username', 'db.password', 'db.database', 'db.prefix', 'email', 'singleLanguage', 'mainpage'], 'required'],
            [['user.login', 'user.email', 'user.password', 'user.repassword'], 'required'],
            ['mainpage', 'in', ['none', 'news', 'about']],
            [['user.login', 'user.password'], 'length_min', 4],
            ['user.repassword', 'equal', $this->getRequest('user.password', $this->getSubmitMethod())],
            ['user.email', 'email'],
            ['multiLanguage', 'used'],
            ['db.driver', 'in', ['mysql', 'pgsql', 'sqlite']],
            ['email', 'email'],
            ['singleLanguage', 'in', App::$Translate->getAvailableLangs()],
            ['db', 'Apps\Model\Install\Main\FormInstall::filterCheckDb']
        ];
    }

    /**
     * Save configurations build by installer interface
     */
    public function make()
    {
        // prepare configurations to save
        /** @var array $cfg */
        $cfg = App::$Properties->getAll('default');
        $this->before();
        $cfg['baseDomain'] = $this->baseDomain;
        $cfg['database'] = $this->db;
        $cfg['adminEmail'] = $this->email;
        $cfg['singleLanguage'] = $this->singleLanguage;
        $cfg['multiLanguage'] = (bool)$this->multiLanguage;
        $cfg['passwordSalt'] = '$2a$07$' . Str::randomLatinNumeric(mt_rand(21, 30)) . '$';
        $cfg['debug']['cookie']['key'] = 'fdebug_' . Str::randomLatinNumeric(mt_rand(4, 16));
        $cfg['debug']['cookie']['value'] = Str::randomLatinNumeric(mt_rand(32, 128));

        // initialize migrations table
        App::$Database->getConnection('install')->getSchemaBuilder()->create('migrations', function ($table){
            $table->increments('id');
            $table->string('migration', 128)->unique();
            $table->timestamps();
        });

        // import migrations
        $manager = new MigrationsManager(null, 'install');
        $search = $manager->search(null, false);
        $manager->makeUp($search);

        // insert admin user
        $user = new User();
        $user->setConnection('install');
        $user->login = $this->user['login'];
        $user->email = $this->user['email'];
        $user->role_id = 4;
        $user->password = Security::password_hash($this->user['password'], $cfg['passwordSalt']);
        $user->save();

        $profile = new Profile();
        $profile->setConnection('install');
        $profile->user_id = $user->id;
        $profile->save();

        // set installation version
        $system = new System();
        $system->setConnection('install');
        $system->var = 'version';
        $system->data = Version::VERSION;
        $system->save();

        // write config data
        App::$Properties->writeConfig('default', $cfg);
        // make routing configs based on preset property
        $routing = [];
        switch ($this->mainpage) {
            case 'news':
                $routing = [
                    'Alias' => [
                        'Front' => [
                            '/' => '/content/list/news',
                            '/about' => '/content/read/page/about-page'
                        ]
                    ]
                ];
                break;
            case 'about':
                $routing = [
                    'Alias' => [
                        'Front' => [
                            '/' => '/content/read/page/about-page'
                        ]
                    ]
                ];
                break;
        }
        // write routing configurations
        App::$Properties->writeConfig('routing', $routing);
        // write installer lock
        File::write('/Private/Install/install.lock', 'Installation is locked!');
    }


    /**
     * Check database connection filter
     * @return bool
     */
    public function filterCheckDb()
    {
        App::$Database->addConnection($this->db, 'install');

        try {
            App::$Database->getConnection('install')->getDatabaseName();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}