<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\Content as ContentEntity;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Apps\Model\Front\Content\EntityContentRead;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\String;

class Content extends FrontAppController
{
    public function actionIndex()
    {
        $this->response = 'Welcome index demo action of Content';
    }

    public function actionList()
    {
        $path = App::$Request->getPathWithoutControllerAction();
        $configs = $this->getConfigs();
        $page = (int)App::$Request->query->get('page');
        $itemCount = (int)$configs['itemPerCategory'];
        $offset = $page * $itemCount;

        // generate category array
        $categoryIds = [];
        if ((int)$configs['multiCategories'] === 1) {
            $categoryData = ContentCategory::where('path', 'like', $path . '%')->get(['id'])->toArray();
            if (count($categoryData) < 1) {
                throw new NotFoundException();
            }
            $categoryIds = Arr::ploke('id', $categoryData);
        } else {
            $categoryData = ContentCategory::getByPath($path);
            if ($categoryData === null || $categoryData === false) {
                throw new NotFoundException();
            }
            $categoryIds[] = $categoryData->toArray()['id'];
        }

        $contentQuery = ContentEntity::whereIn('category_id', $categoryIds)->get();
        var_dump($contentQuery);

        $this->response = 'List category';
    }

    public function actionRead()
    {
        // get raw path without controller-action
        $rawPath = App::$Request->getPathWithoutControllerAction();
        $arrayPath = explode('/', $rawPath);
        // get category and content item path as string
        $contentPath = array_pop($arrayPath);
        $categoryPath = implode('/', $arrayPath);

        // try to find category object by string pathway
        $categoryRecord = ContentCategory::getByPath($categoryPath);

        // if no categories are available for this path - throw exception
        if ($categoryRecord === null || $categoryRecord->count() < 1) {
            throw new NotFoundException();
        }

        // try to find content entity record
        $contentRecord = ContentEntity::where('path', '=', $contentPath);
        $trash = false;

        // if no entity is founded for this path lets try to find on trashed
        if ($contentRecord === null || $contentRecord->count() !== 1) {
            // check if user can access to content list on admin panel
            if (!App::$User->isAuth() || !App::$User->identity()->getRole()->can('Admin/Content/Index')) {
                throw new NotFoundException();
            }
            // lets try to find in trashed
            $contentRecord->withTrashed();
            // no way, lets throw exception
            if ($contentRecord->count() !== 1) {
                throw new NotFoundException();
            }
            // set trashed marker for this item
            $trash = true;
        }

        // lets init entity model for content transfer to view
        $model = new EntityContentRead($categoryRecord, $contentRecord->first());

        $this->response = App::$View->render('read_content', [
            'model' => $model,
            'trash' => $trash
        ]);
    }
}