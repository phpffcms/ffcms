<?php

namespace Apps\Controller\Admin\Main;


use Apps\Model\Admin\Main\CollectionSearchResults;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSearch
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSearch
{
    /**
     * Process search action
     * @return string|null
     * @throws ForbiddenException
     */
    public function search(): ?string
    {
        $query = App::$Security->strip_tags($this->request->query->get('search'), null);
        if (!Any::isStr($query) || Str::likeEmpty($query) || Str::length($query) > static::SEARCH_QUERY_MAX_LENGTH) {
            throw new ForbiddenException(__('Wrong query format'));
        }

        $model = new CollectionSearchResults($query, 10);
        App::$Event->run(static::SEARCH_EVENT_NAME, [
            'model' => $model
        ]);

        return $this->view->render('main/search', [
            'model' => $model
        ]);
    }
}