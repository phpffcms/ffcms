<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormCommentDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDelete
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDelete
{
    /**
     * Delete comments and answers single or multiply items
     * @param string $type
     * @param string $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function delete(string $type, ?string $id = null): ?string
    {
        // sounds like a multiply delete definition
        if (!$id || (int)$id < 1) {
            $ids = $this->request->query->get('selected');
            if (!Any::isArray($ids) || !Arr::onlyNumericValues($ids)) {
                throw new NotFoundException('Bad conditions');
            }

            $id = $ids;
        } else {
            $id = [$id];
        }

        // prepare query to db
        /** @var CommentPost|CommentAnswer $query */
        $query = null;
        switch ($type) {
            case self::TYPE_COMMENT:
                $query = CommentPost::whereIn('id', $id);
                break;
            case self::TYPE_ANSWER:
                $query = CommentAnswer::whereIn('id', $id);
                break;
        }

        // check if result is not empty
        if (!$query || $query->count() < 1) {
            throw new NotFoundException(__('No comments found for this condition'));
        }

        // initialize model
        $model = new FormCommentDelete($query, $type);

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Comments or answers are successful deleted!'));
            $this->response->redirect('comments/' . ($type === self::TYPE_ANSWER ? 'answerlist' : 'index'));
        }

        // render view
        return $this->view->render('comments/delete', [
            'model' => $model
        ]);
    }
}
