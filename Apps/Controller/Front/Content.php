<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\Content as ContentEntity;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Apps\Model\Front\Content\EntityCategoryList;
use Ffcms\Core\Exception\NotFoundException;
use Apps\Model\Front\Content\EntityContentRead;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Content. Controller of content app - content and categories.
 * @package Apps\Controller\Front
 */
class Content extends FrontAppController
{
    const TAG_PER_PAGE = 50;

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
     * @return string
     */
    public function actionList()
    {
        $path = App::$Request->getPathWithoutControllerAction();
        $configs = $this->getConfigs();
        $page = (int)App::$Request->query->get('page');
        $itemCount = (int)$configs['itemPerCategory'];

        // build special model with content list and category list information
        $model = new EntityCategoryList($path, $configs, $page);

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['content/list', $path],
            'page' => $page,
            'step' => $itemCount,
            'total' => $model->getContentCount()
        ]);

        // drow response view
        return App::$View->render('list', [
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
     * @return string
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

        return App::$View->render('read', [
            'model' => $model,
            'trash' => $trash,
            'configs' => $this->getConfigs()
        ]);
    }

    /**
     * List latest by created_at content items contains tag name
     * @param string $tagName
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionTag($tagName)
    {
        $configs = $this->getConfigs();
        // check if tags is enabled
        if ((int)$configs['keywordsAsTags'] !== 1) {
            throw new NotFoundException(__('Tag system is disabled'));
        }

        // remove spaces and other shits
        $tagName = trim($tagName);

        // check if tag is not empty
        if (Str::likeEmpty($tagName) || Str::length($tagName) < 2) {
            throw new NotFoundException(__('Tag is empty or is too short!'));
        }

        // get equal rows order by creation date
        $records = ContentEntity::where('meta_keywords', 'like', '%' . $tagName . '%')->orderBy('created_at', 'DESC')->take(self::TAG_PER_PAGE);
        // check if result is not empty
        if ($records->count() < 1) {
            throw new NotFoundException(__('Nothing founded'));
        }

        // render response
        return App::$View->render('tag', [
            'records' => $records->get(),
            'tag' => App::$Security->strip_tags($tagName)
        ]);
    }

    public function actionRss()
    {
        $path = App::$Request->getPathWithoutControllerAction();
    }
}
