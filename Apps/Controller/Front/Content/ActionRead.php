<?php

namespace Apps\Controller\Front\Content;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Front\Content\EntityContentRead;
use Apps\Model\Front\Content\EntityContentSearch;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

/**
 * Trait ActionRead
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionRead
{
    /**
     * Show content item
     * @throws NotFoundException
     * @return string
     */
    public function read(): ?string
    {
        // get raw path without controller-action
        $rawPath = $this->request->getPathWithoutControllerAction();
        $arrayPath = explode('/', $rawPath);
        // get category and content item path as string
        $contentPath = array_pop($arrayPath);
        $categoryPath = implode('/', $arrayPath);

        // try to find category object by string pathway
        $categoryRecord = ContentCategory::getByPath($categoryPath);

        // if no categories are available for this path - throw exception
        if (!$categoryRecord || $categoryRecord->count() < 1) {
            throw new NotFoundException(__('Page not found'));
        }

        // try to find content entity record
        $contentRecord = ContentEntity::where('path', '=', $contentPath)
            ->where('category_id', '=', $categoryRecord->id);
        $trash = false;

        // if no entity is founded for this path lets try to find on trashed
        if (!$contentRecord || $contentRecord->count() !== 1) {
            // check if user can access to content list on admin panel
            if (!App::$User->isAuth() || !App::$User->identity()->role->can('Admin/Content/Index')) {
                throw new NotFoundException(__('Content not found!'));
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
        $search = null;
        // check if similar search is enabled for item category
        if ((bool)$model->getCategory()->getProperty('showSimilar') && !$trash) {
            $search = new EntityContentSearch($model->title, $model->id, $model->getCategory()->id);
        }

        // define read event
        App::$Event->run(static::EVENT_CONTENT_READ, [
            'model' => $model
        ]);

        // render view output
        return $this->view->render('content/read', [
            'model' => $model,
            'search' => $search,
            'trash' => $trash,
            'configs' => $this->getConfigs()
        ]);
    }
}
