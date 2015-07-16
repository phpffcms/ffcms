<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;

class User extends ApiController
{

    /**
     * Chekc user auth.
     * @throws JsonException
     */
    public function actionAuth()
    {
        if (!App::$User->isAuth()) {
            throw new JsonException('No auth');
        }

        $this->response = json_encode([
            'status' => 1,
            'data' => 'Auth done'
        ]);
    }

}