<?php

namespace Apps\Controller\Admin\Content;

use Apps\ActiveRecord\Content as ContentEntity;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Templex\Url\Url;

/**
 * Trait ActionIndex
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List content items
     * @return string|null
     */
    public function index(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        $query = null;
        // get query type (trash, category, all)
        $type = $this->request->query->get('type');
        if ($type === 'trash') {
            $query = ContentEntity::onlyTrashed();
        } elseif ($type === 'moderate') { // only items on moderate
            $query = ContentEntity::where('display', '=', 0);
        } elseif (Any::isInt($type)) { // sounds like category id ;)
            $query = ContentEntity::where('category_id', '=', (int)$type);
        } else {
            $query = new ContentEntity();
            $type = 'all';
        }

        // calculate total items count for pagination
        $total = $query->count();

        // build listing objects
        $records = $query->with('category')
            ->orderBy('important', 'DESC')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('content/index', [
            'records' => $records,
            'pagination' => [
                'url' => ['content/index', null, ['type' => $type]],
                'page' => $page,
                'step' => self::ITEM_PER_PAGE,
                'total' => $total
            ],
            'type' => $type
        ]);
    }
}
