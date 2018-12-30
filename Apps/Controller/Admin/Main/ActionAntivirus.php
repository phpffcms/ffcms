<?php

namespace Apps\Controller\Admin\Main;


use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionAntivirus
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionAntivirus
{
    /**
     * Render antivirus page
     * @return string|null
     */
    public function antivirus(): ?string
    {
        return $this->view->render('main/antivirus');
    }
}