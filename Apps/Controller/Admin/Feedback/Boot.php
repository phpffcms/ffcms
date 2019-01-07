<?php

namespace Apps\Controller\Admin\Feedback;


use Apps\ActiveRecord\FeedbackAnswer;
use Apps\ActiveRecord\FeedbackPost;
use Apps\Controller\Admin\Main;
use Apps\Model\Admin\Main\AbstractSearchItem;
use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Text;

trait Boot
{
    /**
     * Boot hook
     * @return void
     */
    public static function boot(): void
    {
        App::$Event->on(Main::SEARCH_EVENT_NAME, function ($model) {
            /** @var CollectionSearchResults $model */
            $records = FeedbackPost::search($model->getQuery())
                ->take($model->getLimit())
                ->get();

            $records->each(function($item) use ($model) {
                /** @var FeedbackPost $item */
                $title = App::$Translate->get('Feedback', 'Feedback post #%id%', ['id' => $item->id]);
                $text = App::$Security->strip_tags($item->message);
                $snippet = Text::snippet($text);

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($snippet);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('feedback/read', [$item->id]);
                $res->setMarker('Feedback');

                $model->add($res);
            });

            // find answers
            $records = FeedbackAnswer::search($model->getQuery())
                ->take($model->getLimit())
                ->get();
            $records->each(function ($item) use ($model) {
                /** @var FeedbackAnswer $item */
                $title = App::$Translate->get('Feedback', 'Feedback answer #%id%', ['id' => $item->id]);
                $text = App::$Security->strip_tags($item->message);
                $snippet = Text::snippet($text);

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($snippet);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('feedback/read', [$item->feedback_id]);
                $res->setMarker('Feedback');

                $model->add($res);
            });
        });
    }
}