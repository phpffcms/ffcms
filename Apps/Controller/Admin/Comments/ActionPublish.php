<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormCommentModerate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionPublish
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionPublish
{
    /**
     * Moderate guest comments and answer - make it publish
     * @param string $type
     * @param string|null $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function publish(string $type, ?string $id = null): ?string
    {
        // check if it multiple accept ids
        if ($id === null || (int)$id < 1) {
            $ids = $this->request->query->get('selected');
            if (!Any::isArray($ids) || !Arr::onlyNumericValues($ids)) {
                throw new NotFoundException('Bad conditions');
            }
            $id = $ids;
        } else {
            $id = [$id];
        }

        // build query
        $query = null;
        switch ($type) {
            case static::TYPE_COMMENT:
                $query = CommentPost::whereIn('id', $id)->where('moderate', '=', 1);
                break;
            case static::TYPE_ANSWER:
                $query = CommentAnswer::whereIn('id', $id)->where('moderate', '=', 1);
                break;
        }

        // check if result is not empty
        if ($query === null || $query->count() < 1) {
            throw new NotFoundException(__('No comments found for this condition'));
        }

        // initialize moderation model
        $model = new FormCommentModerate($query, $type);

        // check if form is submited
        if ($model->send()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Comments or answers are successful published'));
            $this->response->redirect('comments/' . ($type === 'answer' ? 'answerlist' : 'index'));
        }

        return $this->view->render('publish', [
            'model' => $model
        ]);
    }
}
