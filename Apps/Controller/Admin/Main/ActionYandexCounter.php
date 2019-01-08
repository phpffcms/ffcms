<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\FormYandexCounter;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Yandex\Metrika\Client;

/**
 * Trait ActionYandexCounter
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionYandexCounter
{
    /**
     * Select counter from api ids
     * @return string|null
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function yandexCounter(): ?string
    {
        $configs = App::$Properties->get('oauth', 'Yandex');

        $client = new Client($configs['token'], 0);
        $counters = $client->getCountersList();

        // check if counters exist
        if (!$counters || !Any::isArray($counters) || count($counters) < 1) {
            throw new ForbiddenException(__('No active counters found'));
        }

        // initialize select model
        $model = new FormYandexCounter($counters);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Yandex.metrika connected successful'));
            $this->response->redirect('main/index');
        }

        return $this->view->render('main/yandex_counter', [
            'model' => $model
        ]);
    }
}