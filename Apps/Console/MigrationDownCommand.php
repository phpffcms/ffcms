<?php

namespace Apps\Console;


use Apps\ActiveRecord\Migration;
use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationRevertCommand. Revert installed migrations
 * @package Apps\Console
 */
class MigrationDownCommand extends Command
{
    /**
     * Register command
     */
    public function configure()
    {
        $this->setName('migration:down')
            ->setDescription('Revert single migration by name or date or search query')
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name or date(Y-m-d format) to find all installed migrations');
    }

    /**
     * Search all installed migrations by passed argument and ask to revert it
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get search migration name
        $name = $input->getArgument('name');
        // find in migration table by name
        $query = new Migration();
        if ($this->dbConnection !== null) {
            $query->setConnection($this->dbConnection);
        }
        $query = $query->where('migration', 'like', '%' . $name . '%');
        if ($query->count() < 1) {
            $output->writeln('No migrations found by this name');
            return;
        }

        // list all found migrations and aks to revert it
        foreach ($query->get() as $item) {
            /** @var Migration $item */
            $fullName = $item->migration;
            if ($this->confirm('Are you sure to revert: ' . $fullName)) {
                $path = '/Private/Migrations/' . $fullName . '.php';
                File::inc($path, false, false);
                $className = Str::firstIn($fullName, '-');
                // check if migration class exist and run "down" method
                if (class_exists($className) && is_a($className, 'Ffcms\Core\Migrations\MigrationInterface', true)) {
                    $class = new $className($fullName);
                    $class->down();
                    $output->writeln('Migration are successful revert: ' . $fullName);
                } else {
                    $output->writeln('Migration revert failed: ' . $fullName);
                }
            }
        }
    }
}