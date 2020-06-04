<?php

namespace Apps\Console;


use Apps\ActiveRecord\System;
use Extend\Version;
use Ffcms\Console\Command;
use Ffcms\Console\Console;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Managers\MigrationsManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MainInstallCommand. Install cms
 * @package Apps\Console
 */
class MainInstallCommand extends Command
{
    /**
     * Register installation command and used options
     */
    public function configure()
    {
        $this->setName('main:install')
            ->setDescription('Install ffcms via command line. Shoud be used for experienced users only!!!')
            ->addOption('driver', 'driver', InputOption::VALUE_OPTIONAL, 'Set type of used database driver. Allowed: mysql, pgsql, sqlite')
            ->addOption('host', 'host', InputOption::VALUE_OPTIONAL, 'Set connection host of .sqlite file location folder')
            ->addOption('user', 'user', InputOption::VALUE_OPTIONAL, 'Set database connection user name')
            ->addOption('password', 'password', InputOption::VALUE_OPTIONAL, 'Set password for database user connection')
            ->addOption('dbname', 'dbname', InputOption::VALUE_OPTIONAL, 'Set database name')
            ->addOption('prefix', 'prefix', InputOption::VALUE_OPTIONAL, 'Set database tables constant prefix')
            ->addOption('email', 'email', InputOption::VALUE_OPTIONAL, 'Set website email')
            ->addOption('domain', 'domain', InputOption::VALUE_OPTIONAL, 'Set website main domain')
            ->addOption('mit', 'mit', InputOption::VALUE_OPTIONAL, 'Set yes if you agree with MIT license requirements in /LICENSE file')
            ->setHelp("This tools help to install ffcms in console. Also this can help to install many copy of ffcms automaticaly. 
You can use installation in 1 short command:
\t~\$:php console.php main:install --drv='mysql' --host='127.0.0.1' --user='root' --password='rootpass' --dbname='ffcms' --prefix='ffcms_'
Also you can manually pass all params after running install command:
\t~\$:php console.php main:install
Good luck ;)");
    }

    /**
     * Install cms - database, configs, etc
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // check if installation is locked
        if (File::exist('/Private/Install/install.lock')) {
            $output->writeln('Installation is locked! Please delete /Private/Install/install.lock');
            return 0;
        }
        // show license agreement
        $license = File::read('/LICENSE');
        $output->write($license, PHP_EOL);

        // check if user agree with license terms
        if ($input->getOption('mit') !== 'yes') {
            if (!$this->confirm('Are you accept this license terms?', false)) {
                $output->writeln('You are deny license agreement, installation is rejected');
                return 0;
            }
        }

        // read old & try to get newest configs
        $configs = Console::$Properties->get('database');
        $newConfigs = [];
        $newConfigs['driver'] = $this->optionOrAsk('driver', 'Database driver(mysql|pgsql)', 'mysql');
        $newConfigs['host'] = $this->optionOrAsk('host', 'Database host', '127.0.0.1');
        $newConfigs['username'] = $this->optionOrAsk('user', 'Database user', 'root');
        $newConfigs['password'] = $this->optionOrAsk('password', 'Database password', 'rootpwd');
        $newConfigs['database'] = $this->optionOrAsk('dbname', 'Database name', 'ffcms');
        $newConfigs['prefix'] = $this->optionOrAsk('prefix', 'Database table prefix', 'ffcms_');

        // merge configs and add new connection
        $dbConf = Arr::merge($configs, $newConfigs);
        Console::$Database->addConnection($dbConf, 'install');

        // check if connection is established
        try {
            Console::$Database->getConnection('install')->getPdo();
        } catch (\Exception $e) {
            $output->writeln('Test database connection with new data is FAILED! Please, try to make it with right connection data');
            return 0;
        }

        $output->writeln('=== Merge migrations and prepare installation');

        // implement migrations
        $migrationInstall = $this->getApplication()->find('migration:install');
        $migrationInstall->setDbConnection('install');
        $migrationInstall->run(new ArrayInput([]), $output);

        $migrationManager = new MigrationsManager(null, 'install');
        $search = $migrationManager->search(null, false);
        $migrationManager->makeUp($search);

        // add system info about current install version
        $system = new System();
        $system->setConnection('install');
        $system->var = 'version';
        $system->data = Version::VERSION;
        $system->save();

        $email = $this->optionOrAsk('email', 'Website email', 'root@localhost.ltd');
        $domain = $this->optionOrAsk('domain', 'Website domain', 'localhost.ltd');

        // save configurations to /Private/Default.php
        $output->writeln('=== Writing configurations');
        $chmod = $this->getApplication()->find('main:chmod');
        $chmod->run(new ArrayInput([]), $output);
        /** @var array $allCfg */
        $allCfg = Console::$Properties->getAll('default');
        $allCfg['database'] = $dbConf;
        $allCfg['adminEmail'] = $email;
        $allCfg['baseDomain'] = $domain;
        $output->writeln('Generate security cookies for debug panel');
        $allCfg['debug']['cookie']['key'] = 'fdebug_' . Crypt::randomString(mt_rand(8, 32));
        $allCfg['debug']['cookie']['value'] = Crypt::randomString(mt_rand(32, 128));
        // write config data
        $writeCfg = Console::$Properties->writeConfig('default', $allCfg);
        if ($writeCfg !== true) {
            $output->writeln('File /Private/Config/Default.php is unavailable to write data!');
            return 0;
        }
        File::write('/Private/Install/install.lock', 'Install is locked');
        $output->writeln('Congratulations! FFCMS are successful installed. Used version: ' . Version::VERSION . ' since ' . Version::DATE);
        $output->writeln('');
        $output->writeln('> Please, use "php console.php main:adduser" to add admin account(set role=4) or you are unavailable to manage cms.');

        return 0;
    }

}