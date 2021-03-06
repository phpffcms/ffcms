<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\Search\EntitySearchMain;
use Extend\Core\Arch\FrontAppController as Controller;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Search. Search app front controller
 * @package Apps\Controller\Front
 */
class Search extends Controller
{
    const EVENT_SEARCH_RUN = 'search.run';
    const QUERY_MAX_LENGTH = 100;

    /**
     * Main search method
     * @return string
     * @throws NotFoundException
     */
    public function actionIndex()
    {
        // get search query value from GET headers
        $query = (string)$this->request->query->get('query', null);
        // strip html tags
        $query = App::$Security->strip_tags(trim($query));
        // get configs
        $configs = $this->getConfigs();

        // check search query length
        if (!Any::isStr($query) || Str::likeEmpty($query) || Str::length($query) < (int)$configs['minLength']) {
            throw new NotFoundException(__('Search query is too short!'));
        }

        // prevent sh@t query's with big length
        if (Str::length($query) > static::QUERY_MAX_LENGTH) {
            throw new NotFoundException(__('Search query is too long!'));
        }

        // initialize search controller model
        $model = new EntitySearchMain($query, $configs);

        // register search event to allow extend it model results
        App::$Event->run(static::EVENT_SEARCH_RUN, [
            'model' => $model
        ]);

        // render output view with search result
        return $this->view->render('search/index', [
            'model' => $model,
            'query' => $query
        ]);
    }
}
