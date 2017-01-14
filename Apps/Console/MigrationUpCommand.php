<?php

namespace Apps\Console;


use Apps\ActiveRecord\Migration;
use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Managers\MigrationsManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationApplyCommand. Apply all migrations in db
 * @package Apps\Console
 */
class MigrationUpCommand extends Command
{
    /**
     * Set command
     */
    public function configure()
    {
        $this->setName('migration:up')
            ->setDescription('Apply all migrations to database')
            ->addArgument('name', InputArgument::OPTIONAL, 'Set search name or date (Y-m-d-H-i-s) for migration file');
    }

    /**
     * Apply all migrations into database without always apply'd
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        // run migration manager - find migrations
        $manager = new MigrationsManager(null, $this->dbConnection);
        $search = $manager->search($name, false);
        if (!Obj::isArray($search) || count($search) < 1) {
            $output->writeln('No migrations found');
            return;
        }

        // require confirmation from user each ever migration file
        $fired = false;
        foreach ($search as $migration) {
            if (!$this->confirm('Are you sure to apply migration: ' . $migration, true)) {
                continue;
            }
            $manager->makeUp($migration);
            $fired =  true;
        }

        if ($fired) {
            $output->writeln('All available migrations applied. Done.');
        } else {
            $output->writeln('No migrations executed.');
        }
    }
}