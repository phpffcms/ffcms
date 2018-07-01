<?php

namespace Apps\Controller\Api;

use Apps\Model\Basic\Antivirus;
use elFinder;
use elFinderConnector;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Main. Basic api features for ffcms
 * @package Apps\Controller\Api
 */
class Main extends ApiController
{
    /**
     * Test action
     * @return string
     */
    public function actionIndex(): ?string
    {
        $this->setJsonHeader();
        return json_encode(['status' => 1, 'value' => 'Welcome, man!']);
    }

    /**
     * Elfinder injector file listing
     * @throws ForbiddenException
     */
    public function actionFiles()
    {
        $user = App::$User->identity();

        if (!$user || !$user->role->can('admin/main/files')) {
            throw new ForbiddenException('This action is not allowed!');
        }

        $this->setJsonHeader();
        $connector = new elFinderConnector(new elFinder([
            'locale' => '',
            'roots' => [
                [
                    'driver' => 'LocalFileSystem',
                    'path' => root . '/upload/',
                    'URL' => App::$Alias->scriptUrl . '/upload/'
                ]
            ]
        ]));

        $connector->run();
    }

    /**
     * Make scan and display scan iteration data
     * @return string|null
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionAntivirus(): ?string
    {
        $user = App::$User->identity();
        if (!$user || !$user->role->can('admin/main/antivirus')) {
            throw new ForbiddenException('This action is not allowed!');
        }

        $scanner = new Antivirus();

        $this->setJsonHeader();
        return json_encode($scanner->make());
    }

    /**
     * Remove previous scan files
     * @return string
     * @throws ForbiddenException
     */
    public function actionAntivirusclear(): string
    {
        $user = App::$User->identity();
        if (!$user || !$user->role->can('admin/main/antivirus')) {
            throw new ForbiddenException('This action is not allowed!');
        }

        File::remove('/Private/Antivirus/Infected.json');
        File::remove('/Private/Antivirus/ScanFiles.json');

        $this->setJsonHeader();
        return json_encode(['status' => 1]);
    }

    /**
     * Show scan results
     * @return string
     * @throws ForbiddenException
     */
    public function actionAntivirusresults(): string
    {
        $user = App::$User->identity();
        if (!$user || !$user->role->can('admin/main/antivirus')) {
            throw new ForbiddenException('This action is not allowed!');
        }

        $response = null;
        if (!File::exist('/Private/Antivirus/Infected.json')) {
            $response = ['status' => 0];
        } else {
            $data = json_decode(File::read('/Private/Antivirus/Infected.json'));
            $compile = [];
            foreach ($data as $file => $sign) {
                $file = Str::replace('\\', '/', Str::sub($file, strlen(root)));
                $compile[$file][] = $sign;
            }

            $response = ['status' => 1, 'data' => $compile];
        }

        $this->setJsonHeader();
        return json_encode($response);
    }

    /**
     * Download news from ffcms.org server and show it with caching & saving
     * @return string|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function actionNews(): ?string
    {
        $this->setJsonHeader();
        // get ffcms news if cache is not available
        $cache = App::$Cache->getItem('download.ffcms.api.news.' . $this->lang);
        if (!$cache->isHit()) {
            $cache->set(File::getFromUrl('https://ffcms.org/api/api/news?lang=' . $this->lang))
                ->expiresAfter(1440);
        }
        return $cache->get();
    }
}
