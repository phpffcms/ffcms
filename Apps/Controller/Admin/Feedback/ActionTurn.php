<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionTurn
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 */
trait ActionTurn
{
    /**
     * Turn feedback request post - close, open, readed
     * @param string $direction
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function turn(string $direction, string $id): void
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException('Bad is format');
        }
        // try to find record
        $record = FeedbackPost::find($id);
        if (!$record) {
            throw new NotFoundException(__('Feedback request with id %id% is not found', ['id' => $id]));
        }

        // switch operation direction to what we must change
        switch ($direction) {
            case 'open':
                $record->closed = 0;
                $record->save();
                break;
            case 'close':
                $record->closed = 1;
                $record->save();
                break;
            case 'read':
                $record->readed = 1;
                $record->save();
                break;
            default:
                throw new NotFoundException(__('Hack attention'));
                break;
        }

        // add notification of successful changes
        App::$Session->getFlashBag()->add('success', __('Feedback request is changed!'));

        // redirect to feedback post read
        $this->response->redirect('feedback/read/' . $id);
    }
}
