<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionIndex
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List user comments with pagination
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function index(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // initialize active record model
        $query = new CommentPost();

        // make pagination
        $pagination = new SimplePagination([
            'url' => ['comments/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // get result as active records object with offset
        $records = $query->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }
}
