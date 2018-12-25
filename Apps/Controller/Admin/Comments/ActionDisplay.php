<?php

namespace Apps\Controller\Admin\Comments;


use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDisplay
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDisplay
{

    /**
     * Change moderate status in comment/answer and redirect back
     * @param string $type
     * @param int $id
     * @throws NotFoundException
     */
    public function display($type, $id)
    {
        /** @var CommentPost|CommentAnswer|null $record */
        $record = null;
        switch ($type) {
            case self::TYPE_COMMENT:
                $record = CommentPost::find($id);
                break;
            case self::TYPE_ANSWER:
                $record = CommentAnswer::find($id);
                break;
        }

        if (!$record) {
            throw new NotFoundException('No comments found');
        }

        $record->moderate = !$record->moderate;
        $record->save();

        if ($type === self::TYPE_ANSWER) {
            $this->response->redirect('comments/answerlist');
        }

        $this->response->redirect('comments/index');
    }
}