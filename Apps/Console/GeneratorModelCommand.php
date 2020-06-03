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
 * Class GeneratorModelCommand. Add new model based on carcase template
 * @package Apps\Console
 */
class GeneratorModelCommand extends Command
{
    /**
     * Register command and options
     */
    public function configure()
    {
        $this->setName('generator:model')
            ->setDescription('Generate default model template')
            ->addOption('loader', 'loader', InputOption::VALUE_OPTIONAL, 'Set which loader will be used to create model. Example: front, admin, install')
            ->addOption('controller', 'controller', InputOption::VALUE_OPTIONAL, 'Set related controller for this model. This value will be used as sub-directory for model')
            ->addOption('name', 'name', InputOption::VALUE_OPTIONAL, 'Set model name. Example: FormUserCreate, EntityContentShow');
    }

    /**
     * Generate model from template carcase
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // get options from input
        $loader = $this->optionOrAsk('loader', 'Loader name(front|admin|api)', 'front');
        $controller = $this->optionOrAsk('controller', 'Related controller');
        $name = $this->optionOrAsk('name', 'Controller name');

        $loader = ucfirst(Str::lowerCase($loader));
        $controller = ucfirst(Str::lowerCase($controller));
        $name = ucfirst(Str::lowerCase($name));

        // check loader definition
        if (!Arr::in($loader, ['Front', 'Admin', 'Api'])) {
            throw new \Exception('Wrong definition for loader. You shoud use front, admin, api');
        }

        $namespace = 'Apps\Model\\' . $loader . '\\' . $controller;
        $tpl = File::read('/Private/Carcase/Model.tphp');
        $code = Str::replace(['%namespace%', '%name%'], [$namespace, $name], $tpl);
        $path = '/Apps/Model/' . $loader . '/' . $controller . '/' . $name . '.php';
        File::write($path, $code);
        $output->writeln('Model are successful created: ' . $path);

        return 0;
    }

}