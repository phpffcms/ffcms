<?php


namespace Apps\Controller\Admin\Main;


use Apps\ActiveRecord\Spam;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionFilter
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSpam
{
    /**
     * Show filter logs
     * @return string|null
     */
    public function filter(): ?string
    {
        $page = (int)$this->request->query->get('page');
        $ip = $this->request->getClientIp();

        return $ip;
    }

}