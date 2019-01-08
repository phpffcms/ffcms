<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\FormYandexConnect;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionYandexConnect
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionYandexConnect
{

    /**
     * Connect yandex metrika and get token
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function yandexConnect(): ?string
    {
        $configs = App::$Properties->getAll('yandex');

        $model = new FormYandexConnect($configs);
        if ($model->send() && $model->validate()) {
            App::$Session->getFlashBag()->add('success', __('Application ID saved'));
            $model->make();
        }

        return $this->view->render('main/yandex_connect', [
            'model' => $model
        ]);
    }
}