<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratorControllerCommand. Generate controller carcase
 * @package Apps\Console
 */
class GeneratorControllerCommand extends Command
{
    /**
     * Register command and options
     */
    public function configure()
    {
        $this->setName('generator:controller')
            ->setDescription('Generate default controller template')
            ->addOption('loader', 'loader', InputOption::VALUE_OPTIONAL, 'Set which loader will be used to create controller. Example: front, admin, install')
            ->addOption('name', 'name', InputOption::VALUE_OPTIONAL, 'Set new controller name');
    }

    /**
     * Execute generator create controller command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get options from input
        $loader = $this->optionOrAsk('loader', 'Loader name(front|admin|api)', 'front');
        $name = $this->optionOrAsk('name', 'Controller name');

        $loader = ucfirst(Str::lowerCase($loader));
        $name = ucfirst(Str::lowerCase($name));

        // check loader definition
        if (!Arr::in($loader, ['Front', 'Admin', 'Api'])) {
            throw new \Exception('Wrong definition for loader. You shoud use front, admin, api');
        }

        // prepare code & write
        $template = File::read('/Private/Carcase/' . $loader . '/Controller.tphp');
        $code = Str::replace(['%name%'], [$name], $template);

        $savePath = '/Apps/Controller/' . $loader . '/' . $name . '.php';
        File::write($savePath, $code);

        $output->write('Controller are successful created: ' . $savePath);

        return 0;
    }
}