<?php

namespace Apps\Controller\Admin\Main;


use Apps\Controller\Admin\Main;
use Apps\Model\Admin\Main\AbstractSearchItem;
use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Illuminate\Support\Collection;

trait Boot
{
    /**
     * Hook boot for search features
     */
    public static function boot(): void
    {
        App::$Event->on(Main::SEARCH_EVENT_NAME, function ($model) {
            /** @var CollectionSearchResults $model */
            $records = \Apps\ActiveRecord\App::search($model->getQuery())
                ->take($model->getLimit())
                ->get();

            /** @var \Apps\ActiveRecord\App[]|Collection $records */
            $records->each(function($item) use ($model) {
                /** @var \Apps\ActiveRecord\App $item */
                $text = App::$Translate->get('Main', 'Type: %type%, system name: %sys%, version: %version%', [
                    'type' => $item->type,
                    'sys' => $item->sys_name,
                    'version' => $item->version
                ]);

                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle($item->getLocaled('name'));
                $res->setSnippet($text);
                $res->setDate($item->created_at);
                $res->setRelevance((int)$item->relevance);
                $res->setUrl(Str::lowerCase($item->sys_name) . '/index');
                $res->setMarker('App');

                $model->add($res);
            });

            // search in translation files
            $usedLanguage = App::$Request->getLanguage();
            $onlyKeys = false;
            if ($usedLanguage === 'en') {
                $usedLanguage = 'ru'; // use "ru" locale with keys as default
                $onlyKeys = true;
            }

            // prepare search words for input query
            $searchWords = explode(' ', $model->getQuery());
            $usedWords = array_filter($searchWords, function($value) {
                return Str::length($value) > 2;
            });

            if (!$usedWords || count($usedWords) < 1) {
                return;
            }

            $files = File::listFiles('/I18n/Admin/' . $usedLanguage, ['.php']);
            // each translation files
            foreach ($files as $file) {
                // prepare file name and route target
                $name = Str::lastIn($file, DIRECTORY_SEPARATOR, true);
                $route = Str::lowerCase(Str::firstIn($name, '.'));
                if (Arr::in($name, ['Main.php', 'Default.php'])) { // do not process main & defaults
                    continue;
                }
                $lines = File::inc($file, true, false);
                if (!Any::isArray($lines) && count($lines) < 1) {
                    continue;
                }
                // list i18n file translation lines and search for entries
                $filter = array_filter($lines, function($target, $en) use ($usedWords, $onlyKeys) {
                    $enWords = explode(' ', $en);
                    foreach ($enWords as $enWord) {
                        if(Arr::in($enWord, $usedWords)) {
                            return true;
                        }
                    }

                    if (!$onlyKeys) {
                        $targetWords = explode(' ', $target);
                        foreach ($targetWords as $targetWord) {
                            if (Arr::in($targetWord, $usedWords)) {
                                return true;
                            }
                        }
                    }

                    return false;
                }, ARRAY_FILTER_USE_BOTH);

                if (!$filter || count($filter) < 1) {
                    continue;
                }

                $relevance = 10 * (count($filter) / count($files));
                // initialize abstract response pattern
                $res = new AbstractSearchItem();
                $res->setTitle(ucfirst($route));
                $res->setSnippet(implode('; ', $filter));
                $res->setDate(time());
                $res->setRelevance($relevance);
                $res->setUrl($route . '/index');
                $res->setMarker('i18n');

                $model->add($res);
            }
        });
    }
}