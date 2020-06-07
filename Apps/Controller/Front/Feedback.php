<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\FeedbackPost;
use Apps\Model\Front\Feedback\FormAnswerAdd;
use Apps\Model\Front\Feedback\FormFeedbackAdd;
use Extend\Core\Arch\FrontAppController as Controller;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Feedback. Create, read, update and delete app for user feedback
 * @package Apps\Controller\Front
 */
class Feedback extends Controller
{
    const ITEM_PER_PAGE = 10;
    
    /**
     * This action is not allowed there
     * @throws NotFoundException
     */
    public function actionIndex(): ?string
    {
        throw new NotFoundException('Nothing there...');
    }

    /**
     * Add new feedback message action
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionCreate(): ?string
    {
        // get configs
        $configs = $this->getConfigs();
        if (!App::$User->isAuth() && !(bool)$configs['guestAdd']) {
            throw new ForbiddenException(__('Feedback available only for authorized users'));
        }
        
        // initialize model
        $model = new FormFeedbackAdd((bool)$configs['useCaptcha']);
        if ($model->send()) {
            if ($model->validate()) {
                // if validation is passed save data to db and get row
                $record = $model->make();
                App::$Session->getFlashBag()->add('success', __('Your message was added successful'));
                $this->response->redirect('feedback/read/' . $record->id . '/' . $record->hash);
            } else {
                App::$Session->getFlashBag()->add('error', __('Message is not sended! Please, fix issues in form below'));
            }
        }

        // render output view
        return $this->view->render('feedback/create', [
            'model' => $model,
            'useCaptcha' => (bool)$configs['useCaptcha']
        ]);
    }


    /**
     * Read feedback message and answers and work with add answer model
     * @param string $id
     * @param string $hash
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRead(string $id, string $hash): ?string
    {
        if (!Any::isInt($id) || Str::length($hash) < 16 || Str::length($hash) > 64) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        // get feedback post record from database
        /** @var FeedbackPost $recordPost */
        $recordPost = FeedbackPost::where('id', $id)
            ->where('hash', $hash)
            ->first();

        if (!$recordPost) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        $model = null;
        // check if feedback post is not closed for answers
        if (!(bool)$recordPost->closed) {
            // init new answer add model
            $model = new FormAnswerAdd($recordPost);
            // if answer is sender lets try to make it model
            if ($model->send() && $model->validate()) {
                $model->make();
                App::$Session->getFlashBag()->add('success', __('Your answer was added'));
                $model->clearProperties();
            }
        }

        // render output view
        return $this->view->render('feedback/read', [
            'model' => $model,
            'post' => $recordPost,
            'answers' => $recordPost->answers()->get() // get feedback answers
        ]);
    }

    /**
     * Close feedback request from new answers.
     * @param string $id
     * @param string $hash
     * @return string
     * @throws ForbiddenException
     */
    public function actionClose(string $id, string $hash): ?string
    {
        // get feedback post record from database
        /** @var FeedbackPost $record */
        $record = FeedbackPost::where('id', '=', $id)
            ->where('hash', '=', $hash)
            ->where('closed', '=', 0)
            ->first();

        // check does we found it
        if (!$record) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        // check if action is submited
        if ($this->request->request->get('closeRequest', false)) {
            // if created by authorized user
            if ((int)$record->user_id > 0) {
                $user = App::$User->identity();
                // button is pressed not by request creator
                if (!$user || $user->getId() !== (int)$record->user_id) {
                    throw new ForbiddenException(__('This feedback request was created by another user'));
                }
            }

            // switch closed to 1 and make sql query
            $record->closed = true;
            $record->save();

            // add notification and redirect
            App::$Session->getFlashBag()->add('warning', __('Feedback request now is closed!'));
            $this->response->redirect('feedback/read/' . $id . '/' . $hash);
        }

        return $this->view->render('feedback/close', [
            'id' => (int)$id,
            'hash' => $hash
        ]);
    }

    /**
     * List feedback requests messages from authorized user
     * @return string
     * @throws ForbiddenException
     */
    public function actionList(): ?string
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // check if user is authorized or throw exception
        if (!App::$User->isAuth()) {
            throw new ForbiddenException(__('Feedback listing available only for authorized users'));
        }

        // get current user object
        $user = App::$User->identity();

        // initialize query with major condition
        $query = FeedbackPost::where('user_id', '=', $user->getId());
        $totalCount = $query->count();

        // build records object from prepared query using page offset
        $records = $query->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render viewer with parameters
        return $this->view->render('feedback/list', [
            'records' => $records,
            'pagination' => [
                'step' => self::ITEM_PER_PAGE,
                'total' => $totalCount,
                'page' => $page
            ]
        ]);
    }
}
