<?php

namespace Apps\Controller\Front\Content;


use Apps\ActiveRecord\Content;
use Apps\Controller\Front\Search;
use Apps\Model\Front\Search\AbstractSearchResult;
use Apps\Model\Front\Search\EntitySearchMain;
use Ffcms\Core\App;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Helper\Html\Dom;
use Illuminate\Support\Collection;

/**
 * Trait Boot
 * @package Apps\Controller\Front\Content
 */
trait Boot
{
    /**
     * Implement boot features
     */
    public static function boot(): void
    {
        App::$Event->on(Search::EVENT_SEARCH_RUN, function ($model) {
            /** @var EntitySearchMain $model */
            $limit = (int)$model->getConfigs()['itemPerApp'];
            if ($limit < 1) {
                $limit = 1;
            }

            // relevant search by string query
            $records = Content::search($model->query)
                ->where('display', true)
                ->take($limit)
                ->get();

            /** @var Content[]|Collection $records */
            $records->each(function($item) use ($model) {
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