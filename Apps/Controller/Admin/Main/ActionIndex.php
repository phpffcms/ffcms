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
use Ffcms\Yandex\Metrika\Client;

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

        // work with yandex statistics
        $yandexCfg = App::$Properties->getAll('Yandex');
        $tokenLifetime = $yandexCfg['oauth']['expires'];
        $tokenActive = ($tokenLifetime && time() < $tokenLifetime);

        $visits = null;
        $sources = null;
        if ($tokenActive) {
            // initialize yandex.api client
            $client = new Client($yandexCfg['oauth']['token'], $yandexCfg['metrika']['id']);

            // get visit statistics
            $visits = App::$Cache->getItem('metrika.visits');
            if (!$visits->isHit()) {
                $calcVisit = $client->getVisits30days();
                $visits->set($calcVisit)
                    ->expiresAfter(3600); // update 1 times at hour
                App::$Cache->save($visits);
            }
            $visits = $visits->get();

            // get source distribution statistics
            $sources = App::$Cache->getItem('metrika.sources');
            if (!$sources->isHit()) {
                $calcSources = $client->getSourcesSummary30days();
                $sources->set($calcSources)
                    ->expiresAfter(3600);
                App::$Cache->save($sources);
            }
            $sources = array_slice($sources->get(), 0, 5);
        }

        // render view output
        return $this->view->render('main/index', [
            'stats' => $stats,
            'check' => $model,
            'tokenActive' => $tokenActive,
            'yandexCfg' => $yandexCfg,
            'visits' => $visits,
            'sources' => $sources
        ]);
    }
}
