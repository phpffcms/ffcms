<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\FormYandexToken;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionYandexToken
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionYandexToken
{
    /**
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function yandexToken(): ?string
    {
        $cfg = App::$Properties->getAll('Yandex');

        $model = new FormYandexToken($cfg);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Token saved successful. Valid until: %date%', [
                'date' => Date::convertToDatetime(time() + $model->expires, Date::FORMAT_TO_DAY)
            ]));
            $this->response->redirect('main/yandexcounter');
        }

        return $this->view->render('main/yandex_token', [
            'model' => $model
        ]);
    }
}