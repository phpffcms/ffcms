<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Managers\MigrationsManager;
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
            ->addArgument('name', InputArgument::OPTIONAL, 'Migration name or date(Y-m-d format) to find all installed migrations');
    }

    /**
     * Search all installed migrations by passed argument and ask to revert it
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get search migration name
        $name = $input->getArgument('name');
        // initialize migration manager
        $manager = new MigrationsManager(null, $this->dbConnection);
        $search = $manager->search($name, true);
        if (!Any::isArray($search) || count($search) < 1) {
            $output->writeln('No migrations found');
            return;
        }

        // list found migrations and ask to revert each one
        $fired = false;
        foreach ($search as $migration) {
            if (!$this->confirm('Are you sure to revert: ' . $migration)) {
                continue;
            }
            // run down migration
            $manager->makeDown($migration);
            $fired = true;
        }

        // check if anyone executed
        if ($fired) {
            $output->writeln('Migrations are successful revert');
        } else {
            $output->writeln('No migrations to revert');
        }

        return 0;
    }
}