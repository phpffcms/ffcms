<?php

namespace Apps\Controller\Api;

use Apps\Model\Basic\Antivirus;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\String;

class Main extends ApiController
{
    public function actionIndex()
    {
        $this->setJsonHeader();
        $this->response = json_encode(['status' => 1, 'value' => 'Welcome, man!']);
    }

    public function actionFiles()
    {
        $user = App::$User->identity();

        if ($user === null || !$user->isAuth() || !$user->getRole()->can('admin/main/files')) {
            throw new ForbiddenException('This action is not allowed!');
        }

        // legacy lib can throw some shits
        error_reporting(0);
        $this->setJsonHeader();

        $connector = new \elFinderConnector(new \elFinder([
            'locale' => '',
            'roots' => [
                ['driver' => 'LocalFileSystem', 'path' => root . '/upload/', 'URL' => App::$Alias->scriptUrl . '/upload/']
            ]
        ]));

        $connector->run();
    }

    /**
     * Make scan and display scan iteration data
     */
    public function actionAntivirus()
    {
        $scanner = new Antivirus();

        $this->setJsonHeader();
        $this->response = json_encode($scanner->make());
    }

    /**
     * Remove previous scan files
     */
    public function actionAntivirusclear()
    {
        File::remove('/Private/Antivirus/Infected.json');
        File::remove('/Private/Antivirus/ScanFiles.json');

        $this->setJsonHeader();
        $this->response = json_encode(['status' => 1]);
    }

    /**
     * Show scan results
     */
    public function actionAntivirusresults()
    {
        $response = null;
        if (!File::exist('/Private/Antivirus/Infected.json')) {
            $response = ['status' => 0];
        } else {
            $data = json_decode(File::read('/Private/Antivirus/Infected.json'));
            $compile = [];
            foreach ($data as $file => $sign) {
                $file = String::replace('\\', '/', String::substr($file, strlen(root)));
                $compile[$file][] = $sign;
            }

            $response = ['status' => 1, 'data' => $compile];
        }

        $this->setJsonHeader();
        $this->response = json_encode($response);
    }
}