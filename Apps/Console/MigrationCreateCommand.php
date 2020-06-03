<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationCreateCommand. Create new migrations files
 * @package Apps\Console
 */
class MigrationCreateCommand extends Command
{
    /**
     * Configure command
     */
    public function configure()
    {
        $this->setName('migration:create')
            ->setDescription('Create new migration for cms database')
            ->addArgument('name', InputArgument::REQUIRED, 'Set name of new migration. Example: create_demo_table')
            ->addOption('dir', 'dir', InputOption::VALUE_OPTIONAL, 'Set output directory for new migration file. Example: /vendor/myname/package/migrations');
    }

    /**
     * Create new migration php file based on carcase template
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get migration name from params
        $name = $input->getArgument('name');
        $name = Str::lowerCase($name);
        // get output directory
        $dir = $this->option('dir');
        if ($dir === null || Str::likeEmpty($dir)) {
            $dir = '/Private/Migrations/';
        } else {
            $dir = rtrim($dir, '\\/');
            $dir .= '/';
        }
        // parse table name
        list ($action, $table, $etc) = explode('_', $name);
        if ($table === null || Str::likeEmpty($table)) {
            $table = 'table';
        }
        // create suffix for filename
        $suffix = date('Y-m-d-H-i-s');
        // work with migration template: read & parse & save
        $tpl = File::read('/Private/Carcase/Migration.tphp');
        $classContent = Str::replace(['%class%', '%table%'], [$name, $table], $tpl);
        $fullPath = $dir . $name . '-' . $suffix . '.php';
        File::write($fullPath, $classContent);
        // show success msg
        $output->write('New migration is created: ' . $fullPath);

        return 0;
    }
}