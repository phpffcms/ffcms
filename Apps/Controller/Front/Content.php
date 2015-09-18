<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\Content as ContentEntity;
use Apps\Model\Front\Content\EntityCategoryRead;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Apps\Model\Front\Content\EntityContentRead;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\String;

class Content extends FrontAppController
{
    /**
     * Index is forbidden
     * @throws NotFoundException
     */
    public function actionIndex()
    {
        throw new NotFoundException();
    }

    /**
     * List category content
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionList()
    {
        $path = App::$Request->getPathWithoutControllerAction();
        $configs = $this->getConfigs();
        $page = (int)App::$Request->query->get('page');
        $itemCount = (int)$configs['itemPerCategory'];
        $offset = $page * $itemCount;

        // generate category array
        $categoryIds = [];
        $categoryData = null;
        $currentCategoryData = null;
        // does it multi-category mod?
        if ((int)$configs['multiCategories'] === 1) {
            $categoryData = ContentCategory::where('path', 'like', $path . '%')->get()->toArray();
            if (count($categoryData) < 1) {
                throw new NotFoundException();
            }
            $categoryIds = Arr::ploke('id', $categoryData);
            // extract current category information
            foreach ($categoryData as $row) {
                if ($row['path'] === $path) {
                    $currentCategoryData = $row;
                }
            }
        } else { // its a single category mod
            $categoryData = ContentCategory::getByPath($path)->toArray();
            if ($categoryData === null || $categoryData === false || count($categoryData) < 1) {
                throw new NotFoundException();
            }
            $currentCategoryData = $categoryData;
            $categoryIds[] = $categoryData['id'];
        }

        // get content item list from depended category id's
        $query = ContentEntity::whereIn('category_id', $categoryIds)->where('display', '=', 1);

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['content/list', $path],
            'page' => $page,
            'step' => $itemCount,
            'total' => $query->count()
        ]);

        // generate result
        $records = $query->skip($offset)->take($itemCount)->orderBy('created_at', 'DESC')->get();

        $model = new EntityCategoryRead($records, $currentCategoryData, $categoryData);

        // drow response view
        $this->response = App::$View->render('list', [
            'model' => $model,
            'pagination' => $pagination,
            'configs' => $configs,
        ]);
    }

    /**
     * Show content item
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
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
        $contentRecord = ContentEntity::where('path', '=', $contentPath)->where('category_id', '=', $categoryRecord->id);
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

        $this->response = App::$View->render('read', [
            'model' => $model,
            'trash' => $trash,
            'configs' => $this->getConfigs()
        ]);
    }
}