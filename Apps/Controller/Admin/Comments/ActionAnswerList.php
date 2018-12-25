<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionAnswerList
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionAnswerList
{
    /**
     * List answers action
     * @return string
     */
    public function answerList(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // get result as active records object with offset
        $records = CommentAnswer::orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('comments/answer_list', [
            'records' => $records,
            'pagination' => [
                'url' => ['comments/answerlist'],
                'page' => $page,
                'step' => self::ITEM_PER_PAGE,
                'total' => CommentAnswer::count()
            ]
        ]);
    }
}
