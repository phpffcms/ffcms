<?php

namespace Apps\Controller\Admin\Content;

use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentEntity;

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
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
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

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['content/index', null, null, ['type' => $type]],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('important', 'DESC')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination,
            'type' => $type
        ]);
    }
}
