<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;

/**
 * Class User. User api features controller
 * @package Apps\Controller\Api
 */
class User extends ApiController
{

    /**
     * Check user auth.
     * @return string
     * @throws JsonException
     */
    public function actionAuth()
    {
        $this->setJsonHeader();
        if (!App::$User->isAuth()) {
            throw new JsonException('No auth');
        }

        return json_encode([
            'status' => 1,
            'data' => 'Auth done'
        ]);
    }

    /**
     * Hybridauth endpoint.
     */
    public function actionEndpoint()
    {
        \Hybrid_Endpoint::process();
    }
}
