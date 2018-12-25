<?php

namespace Apps\Controller\Admin\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormCommentUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionEdit
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionEdit
{
    /**
     * Commentaries and answers edit action
     * @param string $type
     * @param string $id
     * @throws NotFoundException
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function edit(string $type, string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new SyntaxException('Bad arguments');
        }

        // get active record by type and id from active records
        $record = null;
        switch ($type) {
            case static::TYPE_COMMENT:
                $record = CommentPost::find($id);
                break;
            case static::TYPE_ANSWER:
                $record = CommentAnswer::find($id);
                break;
        }

        // check if response is not empty
        if (!$record) {
            throw new NotFoundException(__('Comment is not founded'));
        }

        // init edit model
        $model = new FormCommentUpdate($record, $type);

        // check if data is submited and validated
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Comment or answer is successful updated'));
        }

        // render view
        return $this->view->render('comments/edit', [
            'model' => $model
        ]);
    }
}
