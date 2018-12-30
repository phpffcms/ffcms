<?php

namespace Apps\Controller\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Class ActionFiles
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionFiles
{
    /**
     * Display elfinder file manager
     * @return string|null
     */
    public function files(): ?string
    {
        return $this->view->render('main/files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . $this->request->getLanguage()
        ]);
    }
}