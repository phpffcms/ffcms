<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\ActiveRecord\FeedbackPost;
use Apps\Model\Admin\Feedback\FormUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionUpdate
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionUpdate
{
    /**
     * Edit feedback post or answer
     * @param string $type
     * @param string $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function update(string $type, string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException('Bad id format');
        }
        // get active record based on type (post or answer for post)
        $record = null;
        $postId = $id;
        switch ($type) {
            case 'post':
                $record = FeedbackPost::find($id);
                break;
            case 'answer':
                $record = FeedbackAnswer::find($id);
                if (!$record) {
                    $postId = (int)$record->getFeedbackPost()->id;
                }
                break;
        }

        // try what we got
        if (!$record) {
            throw new NotFoundException(__('Feedback item is not founded'));
        }

        // initialize model
        $model = new FormUpdate($record);
        if ($model->send()) {
            if ($model->validate()) {
                $model->make();
                App::$Session->getFlashBag()->add('success', __('Feedback item are successful changed'));
                $this->response->redirect('feedback/read/' . $postId);
            } else {
                App::$Session->getFlashBag()->add('danger', __('Updating is failed'));
            }
        }

        // render output view
        return $this->view->render('feedback/update', [
            'model' => $model
        ]);
    }
}
