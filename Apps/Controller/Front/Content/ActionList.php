<?php

namespace Apps\Controller\Front\Content;

use Apps\Model\Front\Content\EntityCategoryList;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionList
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionList
{
    /**
     * List category content
     * @return string
     */
    public function listing(): ?string
    {
        $path = $this->request->getPathWithoutControllerAction();
        $configs = $this->getConfigs();
        $page = (int)$this->request->query->get('page', 0);
        $sort = (string)$this->request->query->get('sort', 'newest');
        $itemCount = (int)$configs['itemPerCategory'];

        // build special model with content list and category list information
        $model = new EntityCategoryList($path, $configs, $page, $sort);

        // prepare query string (?a=b) for pagination if sort is defined
        $sortQuery = null;
        if (Arr::in($sort, ['rating', 'views'])) {
            $sortQuery = ['sort' => $sort];
        }
        // calculate total row count
        $totalCount = $model->getContentCount();

        // define list event
        App::$Event->run(static::EVENT_CONTENT_LIST, [
            'model' => $model
        ]);

        // draw response view
        return $this->view->render($model->getTpl(), [
            'model' => $model,
            'pagination' => [
                'page' => $page,
                'total' => $totalCount,
                'step' => $itemCount,
                'url' => ['content/list', [$path], [$sortQuery]]
            ],
            'configs' => $configs,
        ]);
    }
}
