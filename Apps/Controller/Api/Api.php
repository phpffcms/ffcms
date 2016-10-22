<?php

namespace Apps\Controller\Api;


use Extend\Core\Arch\ApiController;
use Apps\ActiveRecord\Content as ContentRecord;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;

/**
 * Class Api. Ffcms official website public API
 * @package Apps\Controller\Api
 */
class Api extends ApiController
{
    private $newsCategories = [2, 4];

    /**
     * Show ffcms latest 10 news from special category as json data
     * @return string
     */
    public function actionNews()
    {
        $this->setJsonHeader();
        $data = [];
        if (App::$Cache->get('api.news.list.'.$this->lang) !== null) {
            $data = App::$Cache->get('api.news.list.'.$this->lang);
        } else {
            $records = ContentRecord::select(['contents.*', 'content_categories.path as cpath', 'content_categories.title as ctitle'])
                ->whereIn('contents.category_id', $this->newsCategories)
                ->join('content_categories', 'content_categories.id', '=', 'contents.category_id', 'left outer')
                ->orderBy('contents.created_at', 'DESC')
                ->take(10)->get()->toArray();

            foreach ($records as $item) {
                $data[] = [
                    'title' => App::$Translate->getLocaleText($item['title']),
                    'date' => Date::humanize($item['created_at']),
                    'url' => 'https://ffcms.org/' . $this->request->getLanguage() . '/content/read/' . $item['cpath'] . '/' . $item['path'],
                    'snippet' => Text::snippet(App::$Translate->getLocaleText($item['text'], $this->request->getLanguage()))
                ];
            }
            App::$Cache->set('api.news.list.'.$this->lang, $data, 3600);
        }

        return json_encode([
            'status' => 1,
            'data' => $data
        ]);
    }
}