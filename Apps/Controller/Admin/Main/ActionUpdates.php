<?php

namespace Apps\Controller\Admin\Main;

use Apps\Model\Admin\Main\EntityUpdate;
use Apps\Model\Admin\Main\FormUpdateDatabase;
use Apps\Model\Admin\Main\FormUpdateDownload;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Templex\Url\Url;

/**
 * Trait ActionUpdates
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionUpdates
{
    /**
     * Make system update
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function updates()
    {
        // initialize models - entity, database, download
        $entityModel = new EntityUpdate();
        $dbModel = new FormUpdateDatabase($entityModel->dbVersion, $entityModel->scriptVersion);
        $downloadModel = new FormUpdateDownload($entityModel->lastInfo['download_url'], $entityModel->lastVersion);

        // find files with sql queries to update if required
        if (!$entityModel->versionsEqual) {
            $dbModel->findUpdateFiles();
            // if submit is pressed make update
            if ($dbModel->send() && $dbModel->validate()) {
                $dbModel->make();
                App::$Session->getFlashBag()->add('success', __('Database updates are successful installed'));
                App::$Response->redirect(Url::to('main/updates'));
            }
        } elseif ($entityModel->haveRemoteNew) { // download full compiled .zip archive & extract files
            if ($downloadModel->send()) {
                if ($downloadModel->make()) {
                    App::$Session->getFlashBag()->add('success', __('Archive with new update are successful downloaded and extracted. Please refresh this page and update database if required'));
                } else {
                    App::$Session->getFlashBag()->add('error', __('In process of downloading and extracting update archive error is occurred. Something gonna wrong'));
                }
            }
        }

        return $this->view->render('main/updates', [
            'entityModel' => $entityModel,
            'dbModel' => $dbModel,
            'downloadModel' => $downloadModel
        ]);
    }
}
