<?php

namespace Apps\Controller\Admin\Content;


use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Apps\Controller\Admin\Main;
use Apps\Model\Admin\Main\AbstractSearchItem;
use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Illuminate\Support\Collection;

/**
 * Trait Boot
 * @package Apps\Controller\Admin\Content
 */
trait Boot
{
    /**
     * Implement search boot features
     * @return void
     */
    public static function boot(): void
    {
        App::$Event->on(Main::SEARCH_EVENT_NAME, function ($model) {
            /** @var CollectionSearchResults $model */
            $limit = $model->getLimit();
            $query = $model->getQuery();

            // relevant search by string query
            $records = Content::whereFullText(['title', 'text'], $query)
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
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($snippet);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('content/update', [$item->id]);
                $res->setMarker('Content');

                $model->add($res);
            });
            // search in categories
            $records = ContentCategory::search($query)
                ->take($limit)
                ->get();

            /** @var ContentCategory[]|Collection $records */
            $records->each(function($item) use ($model) {
                /** @var ContentCategory $item */
                $title = $item->getLocaled('title');
                $text = App::$Security->strip_tags($item->getLocaled('description'));
                $snippet = Text::snippet($text);
                // prevent empty items
                if (Str::likeEmpty($title)) {
                    return;
                }

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($snippet);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('content/categoryupdate', [$item->id]);
                $res->setMarker('Content');

                $model->add($res);
            });
        });
    }
}