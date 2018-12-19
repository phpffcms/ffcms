<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDelete
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDelete
{
    /**
     * Delete feedback post or answer
     * @param string $type
     * @param string $id
     * @return string
     * @throws NotFoundException
     */
    public function delete(string $type, string $id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException('Bad id format');
        }
        // try to get active record by type
        $record = null;
        switch ($type) {
            case 'post':
                $record = FeedbackPost::find($id);
                break;
            case 'answer':
                $record = FeedbackAnswer::find($id);
                break;
        }

        // check if we get the row
        if (!$record) {
            throw new NotFoundException(__('Feedback item is not founded'));
        }

        // if delete is submited
        if ($this->request->request->get('deleteFeedback')) {
            // remove all answers
            if ($type === 'post') {
                FeedbackAnswer::where('feedback_id', '=', $record->id)->delete();
                // remove item
                $record->delete();
                App::$Session->getFlashBag()->add('success', __('Feedback record is successful removed'));
                $this->response->redirect('feedback/index');
            } else {
                // its a answer, lets remove it and redirect back in post
                $postId = $record->feedback_id;
                $record->delete();
                $this->response->redirect('feedback/read/' . $postId);
            }
        }

        // render view
        return $this->view->render('feedback/delete', [
            'type' => $type,
            'record' => $record
        ]);
    }
}
