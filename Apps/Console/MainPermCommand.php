<?php

namespace Apps\Console;


use Ffcms\Console\Command;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MainPermCommand. Parse permission list by controller name & actions
 * @package Apps\Console
 */
class MainPermCommand extends Command
{
    /**
     * Set command and information
     */
    public function configure()
    {
        $this->setName('main:perm')
            ->setDescription('Rebuild permissions list for all available controllers');
    }

    /**
     * Parse exist admin controllers & write list of available permissions
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // default permissions
        $permissions = [
            'global/write',
            'global/modify',
            'global/file',
            'global/all'
        ];
        // admin controllers
        $AdminAppControllers = '/Apps/Controller/Admin/';
        // scan directory
        $scan = File::listFiles($AdminAppControllers, ['.php']);
        foreach ($scan as $file) {
            $className = Str::firstIn(Str::lastIn($file, DIRECTORY_SEPARATOR, true), '.');
            // read as plain text
            $byte = File::read($file);
            preg_match_all('/public function action(\w*?)\(/', $byte, $matches); // matches[0] contains all methods ;)
            if (Obj::isArray($matches[1]) && count($matches[1]) > 0) {
                foreach ($matches[1] as $perm) {
                    $fullPerm = 'Admin/' . $className . '/' . $perm;
                    $permissions[] = $fullPerm;
                    $output->writeln("\tAdd permission: " . $fullPerm);
                }
            }
        }
        // prepare save string
        $stringSave = "<?php \n\nreturn " . Arr::exportVar($permissions) . ';';
        File::write('/Private/Config/Permissions.php', $stringSave);

        $output->writeln('Permissions configuration is successful updated! Founded permissions: ' . count($permissions));
    }

}