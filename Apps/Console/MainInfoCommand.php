<?php

namespace Apps\Console;

use Extend\Version;
use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MainInfoCommand. Display info about cms
 * @package Apps\Console
 */
class MainInfoCommand extends Command
{
    /**
     * Configure command data
     */
    protected function configure()
    {
        $this->setName('main:info')
            ->setDescription('Get system main information');
    }

    /**
     * Show system information about ffcms
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Information about FFCMS package and environment:');
        $output->writeln("\t PHP version: " . phpversion());
        $output->writeln("\t Dist path: " . root);
        $output->writeln("\t Used version: " . Version::VERSION . ' [build: ' . Version::DATE . ']');
        $output->writeln('Information about FFCMS cmf packages:');

        $composerInfo = File::read('/composer.lock');
        if (false !== $composerInfo) {
            $jsonInfo = json_decode($composerInfo);
            foreach ($jsonInfo->packages as $item) {
                $output->writeln("\t Package: " . $item->name . ' => ' . $item->version);
            }
        } else {
            $output->writeln("\t Composer is never be used - no information available.");
        }
    }
}