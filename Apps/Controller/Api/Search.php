<?php

namespace Apps\Controller\Api;

use Ffcms\Core\App;
use Apps\Model\Front\Search\EntitySearchMain;
use Apps\ActiveRecord\Content;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Text;
use Apps\Model\Front\Search\AbstractSearchResult;

/**
 * Class Search. Make search with json response by standard model
 * @package Apps\Controller\Api
 */
class Search extends ApiController
{
    const EVENT_SEARCHAPI_NAME = 'search.api.run';
    const ITEM_PER_PAGE = 3;

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
        $model = new EntitySearchMain($query, [
            "itemPerApp" => static::ITEM_PER_PAGE,
            "minLength" => 3
        ]);


        // register search event to allow extend it model results
        App::$Event->run(static::EVENT_SEARCHAPI_NAME, [
            'model' => $model
        ]);

        // build response by relevance as array
        $response = $model->getResult();
        $result = [];
        foreach ($response as $r) {
            $result[] = [
                'title' => $r->getTitle(),
                'snippet' => $r->getSnippet(),
                'uri' => $r->getUri()
            ];
        }

        return json_encode([
            'status' => 1,
            'count' => count($result),
            'data' => $result
        ]);
    }

    /**
     * Implement boot features
     */
    public static function boot(): void
    {
        App::$Event->on(Search::EVENT_SEARCHAPI_NAME, function ($model) {
            /** @var EntitySearchMain $model */
             // relevant search by string query
            $records = Content::whereFullText(['title', 'text'], $model->query)
                ->where('display', true)
                ->take(static::ITEM_PER_PAGE)
                ->get();

            /** @var Content[]|Collection $records */
            $records->each(function ($item) use ($model) {
                /** @var Content $item */
                $title = $item->getLocaled('title');
                $text = App::$Security->strip_tags($item->getLocaled('text'));
                $snippet = Text::snippet($text);
                // prevent empty items
                if (Str::likeEmpty($title)) {
                    return;
                }

                // initialize abstract response pattern
                $res = new AbstractSearchResult();
                $res->setTitle($title);
                $res->setSnippet($snippet);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUri('/content/read/' . $item->getPath());

                $model->add($res);
            });
        });
    }
}
