<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionRead
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRead
{
    /**
     * List comment - read comment and list answers
     * @param int $id
     * @return string
     * @throws NotFoundException
     */
    public function read($id)
    {
        // find object in active record model
        $record = CommentPost::with(['answers'])->find($id);
        if (!$record) {
            throw new NotFoundException(__('Comment is not founded'));
        }

        // render response
        return $this->view->render('comments/read', [
            'record' => $record
        ]);
    }
}
