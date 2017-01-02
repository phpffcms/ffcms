<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MainChmodCommand. Set chmods for default folders and files
 * @package Apps\Console
 */
class MainChmodCommand extends Command
{
    // dirs to create & chmod
    public static $installDirs = [
        '/upload/', '/upload/user/', '/upload/gallery/', '/upload/images/', '/upload/flash/', '/upload/files/', '/upload/sitemap/',
        '/Private/Cache/', '/Private/Cache/HTMLPurifier/', '/Private/Sessions/', '/Private/Antivirus/', '/Private/Install/',
        '/Private/Config/', '/Private/Config/Default.php', '/Private/Config/Routing.php', '/Private/Config/Cron.php'
    ];

    /**
     * Set command binding & information
     */
    public function configure()
    {
        $this->setName('main:chmod')
            ->setDescription('Automatically set chmod\'s for default ffcms directorieswith read/write permissions');
            //->addOption('secure', 's', InputOption::VALUE_REQUIRED, 'Do you want to apply auto chmod with {0666|0776} permissions instead of 0777. Note you should make right chown & process user group. Answers: yes/no');
    }

    /**
     * Process change chmod for all dirs & files
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        //$secure = $input->getOption('secure');
        foreach (self::$installDirs as $obj) {
            if (Directory::exist($obj)) {
                Directory::recursiveChmod($obj, 0777);
                $output->writeln('Write recursive permissions 0777 for all directories in: ' . $obj);
            } elseif (File::exist($obj)) {
                chmod(root . $obj, 0777);
                $output->writeln('Write permissions 0777 for file: ' . $obj);
            }
        }
    }
}