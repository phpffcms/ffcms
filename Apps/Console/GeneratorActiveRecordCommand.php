<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratorActiveRecordCommand. Generate new active record model based on default carcase
 * @package Apps\Console
 */
class GeneratorActiveRecordCommand extends Command
{
    /**
     * Register command
     */
    public function configure()
    {
        $this->setName('generator:activerecord')
            ->setDescription('Generate ActiveRecord model template')
            ->addOption('name', 'name', InputOption::VALUE_OPTIONAL, 'Set active record model name');
    }

    /**
     * Execute active record create
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get ar name from input
        $name = $this->optionOrAsk('name', 'ActiveRecord name');
        $name = ucfirst(Str::lowerCase($name));

        $tpl = File::read('/Private/Carcase/ActiveRecord.tphp');
        $code = Str::replace('%name%', $name, $tpl);

        $path = '/Apps/ActiveRecord/' . $name . '.php';
        File::write($path, $code);
        $output->writeln('ActiveRecord are successful created: ' . $path);
    }
}