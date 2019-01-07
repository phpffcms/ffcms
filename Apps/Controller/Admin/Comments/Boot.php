<?php

namespace Apps\Controller\Admin\Comments;


use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Controller\Admin\Main;
use Apps\Model\Admin\Main\AbstractSearchItem;
use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Text;
use Illuminate\Support\Collection;

/**
 * Trait Boot
 * @package Apps\Controller\Admin\Comments
 */
trait Boot
{
    /**
     * Boot search hook
     * @return void
     */
    public static function boot(): void
    {
        App::$Event->on(Main::SEARCH_EVENT_NAME, function ($model) {
            /** @var CollectionSearchResults $model */
            $records = CommentPost::search($model->getQuery())
                ->take($model->getLimit())
                ->get();

            /** @var CommentPost[]|Collection $records */
            $records->each(function($item) use ($model) {
                /** @var CommentPost $item */
                $title = App::$Translate->get('Comments', 'Comment #%id%', ['id' => $item->id]);
                $text = Text::snippet(App::$Security->strip_tags($item->message));

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($text);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('comments/read', [$item->id]);
                $res->setMarker('Comment');

                $model->add($res);
            });

            // search comment answers
            $records = CommentAnswer::search($model->getQuery())
                ->take($model->getLimit())
                ->get();

            /** @var CommentAnswer[]|Collection $records */
            $records->each(function($item) use ($model) {
                /** @var CommentAnswer $item */
                $title = App::$Translate->get('Comments', 'Comment answer #%id%', ['id' => $item->id]);
                $text = Text::snippet(App::$Security->strip_tags($item->message));

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($text);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('comments/read', [$item->comment_id]);
                $res->setMarker('Comment');

                $model->add($res);
            });

        });
    }
}