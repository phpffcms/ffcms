<?php


namespace Apps\Controller\Admin;

use Apps\ActiveRecord\FeedbackAnswer;
use Apps\Model\Admin\Feedback\FormAnswerAdd;
use Apps\Model\Admin\Feedback\FormSettings;
use Apps\Model\Admin\Feedback\FormUpdate;
use Extend\Core\Arch\AdminController as Controller;
use Ffcms\Core\App;
use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;

/**
 * Class Feedback. Control and manage feedback request and answers.
 * @package Apps\Controller\Admin
 */
class Feedback extends Controller
{
    const VERSION = '0.1';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    /**
     * List feedback post messages with notifications
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // get feedback posts AR table
        $query = new FeedbackPost();

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['feedback/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // render output
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * Read feedback post and answer and add answer to thread post
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRead($id)
    {
        // find feedback post by id
        $record = FeedbackPost::find($id);
        if ($record === null || $record === false) {
            throw new NotFoundException(__('The feedback message is not founded'));
        }

        // initialize model with answer add if thread is not closed
        $model = null;
        if ((int)$record->closed !== 1) {
            $model = new FormAnswerAdd($record, App::$User->identity()->getId());
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

    /**
     * Edit feedback post or answer
     * @param string $type
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionUpdate($type, $id)
    {
        // get active record based on type (post or answer for post)
        $record = null;
        $postId = $id;
        switch ($type) {
            case 'post':
                $record = FeedbackPost::find($id);
                break;
            case 'answer':
                $record = FeedbackAnswer::find($id);
                if ($record !== null && $record !== false) {
                    $postId = (int)$record->getFeedbackPost()->id;
                }
                break;
        }

        // try what we got
        if ($record === null || $record === false) {
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
        return $this->view->render('update', [
            'model' => $model->filter()
        ]);
    }

    /**
     * Turn feedback request post - close, open, readed
     * @param string $direction
     * @param int $id
     * @return null
     * @throws NotFoundException
     */
    public function actionTurn($direction, $id)
    {
        // try to find record
        $record = FeedbackPost::find($id);
        if ($record === null || $record === false) {
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
        return null;
    }

    /**
     * Delete feedback post or answer
     * @param string $type
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws NotFoundException
     * @throws \Exception
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionDelete($type, $id)
    {
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
        if ($record === null || $record === false) {
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
        return $this->view->render('delete', [
            'type' => $type,
            'record' => $record
        ]);
    }

    /**
     * Settings of feedback application
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings()
    {
        // initialize model and pass configs
        $model = new FormSettings($this->getConfigs());

        // check if form is submited to save data
        if ($model->send()) {
            // is validation passed?
            if ($model->validate()) {
                // save properties
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('feedback/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model->filter()
        ]);
    }

}