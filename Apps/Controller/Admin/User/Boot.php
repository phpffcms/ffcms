<?php

namespace Apps\Controller\Admin\User;


use Apps\ActiveRecord\User;
use Apps\Controller\Admin\Main;
use Apps\Model\Admin\Main\AbstractSearchItem;
use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Illuminate\Support\Collection;

/**
 * Trait Boot
 * @package Apps\Controller\Admin\User
 */
trait Boot
{
    /**
     * Boot features for search
     * @return void
     */
    public static function boot(): void
    {
        App::$Event->on(Main::SEARCH_EVENT_NAME, function ($model) {
            /** @var CollectionSearchResults $model */
            $records = User::with('profile')
                ->search($model->getQuery())
                ->take($model->getLimit())
                ->get();

            /** @var User[]|Collection $records */
            $records->each(function($item) use ($model) {
                /** @var User $item */
                $title = $item->email . '(id=' . $item->id . ')';
                $text = App::$Translate->get('User', 'Email: %email%, nick: %nick%', [
                    'email' => $item->email,
                    'nick' => $item->profile->nick ?? 'id' . $item->id
                ]);

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($title);
                $res->setSnippet($text);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl('user/update', [$item->id]);
                $res->setMarker('User');

                $model->add($res);
            });

        });
    }
}