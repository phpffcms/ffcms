<?php

namespace Apps\Controller\Api;

use Apps\Model\Basic\Antivirus;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

class Main extends ApiController
{
    public function actionIndex()
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

        if ($user === null || !$user->isAuth() || !$user->role->can('admin/main/files')) {
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
     * @return string
     */
    public function actionAntivirus()
    {
        $scanner = new Antivirus();

        $this->setJsonHeader();
        return json_encode($scanner->make());
    }

    /**
     * Remove previous scan files
     * @return string
     */
    public function actionAntivirusclear()
    {
        File::remove('/Private/Antivirus/Infected.json');
        File::remove('/Private/Antivirus/ScanFiles.json');

        $this->setJsonHeader();
        return json_encode(['status' => 1]);
    }

    /**
     * Show scan results
     * @return string
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
     */
    public function actionNews()
    {
        $this->setJsonHeader();
        // get ffcms news if cache is not available
        $news = null;
        if (App::$Cache->get('download.ffcms.api.news.'.$this->lang) !== null) {
            $news = App::$Cache->get('download.ffcms.api.news.'.$this->lang);
        } else {
            $news = File::getFromUrl('https://ffcms.org/api/api/news?lang=' . $this->lang);
            if ($news !== null && !Str::likeEmpty($news)) {
                App::$Cache->set('download.ffcms.api.news.'.$this->lang, $news, 3600 * 12);
            }
        }

        return $news;
    }
}