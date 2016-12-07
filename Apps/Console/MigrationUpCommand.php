<?php

namespace Apps\Console;


use Apps\ActiveRecord\Migration;
use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
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
        // get all installed migrations from db
        $records = new Migration();
        if ($this->dbConnection !== null) {
            $records->setConnection($this->dbConnection);
        }
        $skipped = Arr::ploke('migration', $records->get()->toArray());
        // scan all available migrations
        $migrations = File::listFiles('/Private/Migrations', ['.php'], true);
        if (!Obj::isArray($migrations) || count($migrations) < 1) {
            $output->writeln('No migrations found');
            return;
        }

        // list migrations
        $fired = false;
        foreach ($migrations as $migration) {
            // parse migration fullname and classname
            $fullName = Str::cleanExtension($migration);
            $className = Str::firstIn($fullName, '-');
            // if used search by migration name/date - lets filter this
            if ($name !== null && !Str::likeEmpty($name)) {
                // check if current migration name contains search name
                if (!Str::contains($name, $fullName)) {
                    continue;
                }
            }
            File::inc('/Private/Migrations/' . $migration, false, false);
            // check migration compatability
            if (class_exists($className) && !Arr::in($fullName, $skipped) && is_a($className, 'Ffcms\Core\Migrations\MigrationInterface', true)) {
                if ($name !== null && !Str::likeEmpty($name)) {
                    // require user submit
                    if (!$this->confirm('Are you sure to apply migration: ' . $fullName, true)) {
                        continue;
                    }
                }
                // initialize migration class & run up/seed methods
                $class = new $className($fullName, $this->dbConnection);
                $class->up();
                $class->seed();

                $output->writeln('Apply migration(' . $className . '): ' . $migration);
                $fired = true;
            }
        }

        if ($fired) {
            $output->writeln('All available migrations applied. Done.');
        } else {
            $output->writeln('No new migrations found.');
        }
    }
}