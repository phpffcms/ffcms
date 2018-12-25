<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\View;
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
     * @return string|null
     */
    public function index(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // get result as active records object with offset
        $records = CommentPost::orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('comments/index', [
            'records' => $records,
            'pagination' => [
                'url' => ['comments/index'],
                'page' => $page,
                'step' => self::ITEM_PER_PAGE,
                'total' => CommentPost::count()
            ]
        ]);
    }
}
