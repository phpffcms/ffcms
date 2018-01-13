<?php

namespace Apps\Controller\Api;

use Apps\Model\Front\Search\EntitySearchMain;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Search. Make search with json response by standard model
 * @package Apps\Controller\Api
 */
class Search extends ApiController
{
    /**
     * Print json response for search query based on standard model
     * @return string
     * @throws JsonException
     */
    public function actionIndex(): ?string
    {
        $this->setJsonHeader();
        // get search query as string from request
        $query = $this->request->query->get('query', null);
        if (Str::likeEmpty($query) || Str::length($query) < 2) {
            throw new JsonException('Short query');
        }

        // initialize basic search model
        $model = new EntitySearchMain($query, ['itemPerApp' => 3]);
        $model->make();

        // build response by relevance as array
        $response = $model->getRelevanceSortedResult();

        return json_encode([
            'status' => 1,
            'count' => count($response),
            'data' => $response
        ]);
    }
}
