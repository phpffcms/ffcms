<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Illuminate\Database\Capsule\Manager as DatabaseManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationInstallCommand. Initialize migrations table in db
 * @package Apps\Console
 */
class MigrationInstallCommand extends Command
{
    /**
     * Register migration install cmd
     */
    public function configure()
    {
        $this->setName('migration:install')
            ->setDescription('Initialize migration manager as database tool');
    }

    /**
     * Create migration table if not exists
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // check if migration table exists
        if (DatabaseManager::schema($this->dbConnection)->hasTable('migrations')) {
            $output->write('Migration table is always exists!');
            return;
        }
        DatabaseManager::schema($this->dbConnection)->create('migrations', function ($table){
            $table->increments('id');
            $table->string('migration', 127)->unique();
            $table->timestamps();
        });

        $output->writeln('Migrations table are successful initialized. If you want to apply migrations run migration:up');
    }

}