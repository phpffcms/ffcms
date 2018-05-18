<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\ActiveRecord\FeedbackPost;
use Apps\Model\Admin\Feedback\FormAnswerAdd;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionRead
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRead
{
    /**
     * Read feedback post and answer and add answer to thread post
     * @param string $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function read(string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException('Bad id format');
        }
        // find feedback post by id
        $record = FeedbackPost::with(['user', 'user.profile'])
            ->find($id);
        if ($record === null || $record === false) {
            throw new NotFoundException(__('The feedback message is not founded'));
        }

        // initialize model with answer add if thread is not closed
        $model = null;
        if ((int)$record->closed !== 1) {
            $model = new FormAnswerAdd($record);
            if ($model->send()) {
                if ($model->validate()) {
                    $model->make();
                    App::$Session->getFlashBag()->add('success', __('New answer is successful add'));
                } else {
                    App::$Session->getFlashBag()->add('error', 'Validation failure');
                }
            }
        }

        // render view
        return $this->view->render('read', [
            'record' => $record,
            'model' => $model
        ]);
    }
}
