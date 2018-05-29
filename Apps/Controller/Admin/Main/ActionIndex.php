<?php

namespace Apps\Controller\Admin\Main;

use Apps\Model\Install\Main\EntityCheck;
use Extend\Version;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Environment;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionIndex
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * Index page of admin dashboard
     * @return string|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function index(): ?string
    {
        // get cached statistics
        $rootSize = App::$Cache->getItem('root.size');
        $loadAvg = App::$Cache->getItem('load.avarage');
        if (!$rootSize->isHit()) {
            $calcSize = round(Directory::size('/') / (1024*1000), 2) . ' mb';
            $rootSize->set($calcSize);
            $rootSize->expiresAfter(86400);
            App::$Cache->save($rootSize);
        }
        if (!$loadAvg->isHit()) {
            $loadAvg->set(Environment::loadAverage());
            $loadAvg->expiresAfter(300);
            App::$Cache->save($loadAvg);
        }

        // prepare system statistic
        $stats = [
            'ff_version' => Version::VERSION . ' (' . Version::DATE . ')',
            'php_version' => Environment::phpVersion() . ' (' . Environment::phpSAPI() . ')',
            'os_name' => Environment::osName(),
            'database_name' => App::$Database->connection()->getDatabaseName() . ' (' . App::$Database->connection()->getDriverName() . ')',
            'file_size' => $rootSize->get(),
            'load_avg' => $loadAvg->get()
        ];
        // check directory chmods and other environment features
        $model = new EntityCheck();

        // render view output
        return $this->view->render('main/index', [
            'stats' => $stats,
            'check' => $model
        ]);
    }
}
