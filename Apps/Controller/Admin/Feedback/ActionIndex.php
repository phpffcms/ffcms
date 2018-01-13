<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionIndex
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List feedback post messages with notifications
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function index(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // get feedback posts AR table
        $query = FeedbackPost::with(['answers']);

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['feedback/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }
}
