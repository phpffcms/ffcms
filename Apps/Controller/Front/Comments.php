<?php

namespace Apps\Controller\Front;


use Apps\ActiveRecord\CommentPost;
use Apps\Model\Front\Search\AbstractSearchResult;
use Apps\Model\Front\Search\EntitySearchMain;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;
use Illuminate\Support\Collection;

/**
 * Class Comments
 * @package Apps\Controller\Front
 */
class Comments extends FrontAppController
{
    /**
     * Boot search features
     * @return void
     */
    public static function boot(): void
    {
        App::$Event->on(Search::EVENT_SEARCH_RUN, function ($model) {
            /** @var EntitySearchMain $model */
            $limit = (int)$model->getConfigs()['itemPerApp'];
            if ($limit < 1) {
                $limit = 1;
            }

            $query = CommentPost::search($model->query)
                ->where('moderate', '=', 0)
                ->take($limit)
                ->get();

            /** @var CommentPost[]|Collection $query */
            $query->each(function($item) use ($model){
                /** @var CommentPost $item */
                $snippet = App::$Security->strip_tags($item->message);
                $snippet = Text::snippet($snippet);

                // make unique instance object
                $instance = new AbstractSearchResult();
                $instance->setTitle(App::$Translate->get('Search', 'Comment on the page'));
                $instance->setSnippet($snippet);
                $instance->setUri('/' . $item->app_name . '/comments/' . $item->app_relation_id);
                $instance->setDate($item->created_at);
                $instance->setRelevance((int)$item->relevance);

                // add instance to result set
                $model->add($instance);
            });
        });
    }
}