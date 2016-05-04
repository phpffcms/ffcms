<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\FeedbackPost;
use Apps\Model\Front\Feedback\FormAnswerAdd;
use Apps\Model\Front\Feedback\FormFeedbackAdd;
use Extend\Core\Arch\FrontAppController as Controller;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Obj;
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
    public function actionIndex()
    {
        throw new NotFoundException('Nothing there...');
    }

    /**
     * Add new feedback message action
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionCreate()
    {
        // get configs
        $configs = $this->getConfigs();
        if (!App::$User->isAuth() && (int)$configs['guestAdd'] !== 1) {
            throw new ForbiddenException(__('Feedback available only for authorized users'));
        }

        // initialize model
        $model = new FormFeedbackAdd((int)$configs['useCaptcha'] === 1);
        if ($model->send()) {
            if ($model->validate()) {
                // if validation is passed save data to db and get row
                $record = $model->make();
                App::$Session->getFlashBag()->add('success', __('Your message was added successful'));
                App::$Response->redirect('feedback/read/' . $record->id . '/' . $record->hash);
            } else {
                App::$Session->getFlashBag()->add('error', __('Message is not sended! Please, fix issues in form below'));
            }
        }

        // render output view
        return App::$View->render('create', [
            'model' => $model->filter(),
            'useCaptcha' => (int)$configs['useCaptcha'] === 1
        ]);
    }


    /**
     * Read feedback message and answers and work with add answer model
     * @param int $id
     * @param string $hash
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRead($id, $hash)
    {
        if (!Obj::isLikeInt($id) || Str::length($hash) < 16 || Str::length($hash) > 64) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        // get feedback post record from database
        $recordPost = FeedbackPost::where('id', '=', $id)
            ->where('hash', '=', $hash)
            ->first();

        if ($recordPost === null) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        $userId = App::$User->isAuth() ? App::$User->identity()->getId() : 0;
        $model = null;
        // check if feedback post is not closed for answers
        if ((int)$recordPost->closed === 0) {
            // init new answer add model
            $model = new FormAnswerAdd($recordPost, $userId);
            // if answer is sender lets try to make it model
            if ($model->send() && $model->validate()) {
                $model->make();
                App::$Session->getFlashBag()->add('success', __('Your answer was added'));
                $model->clearProperties();
            }
            // secure display html data
            $model = $model->filter();
        }

        // render output view
        return App::$View->render('read', [
            'model' => $model,
            'post' => $recordPost,
            'answers' => $recordPost->getAnswers()->get() // get feedback answers
        ]);
    }

    /**
     * @param int $id
     * @param string $hash
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionClose($id, $hash)
    {
        // get feedback post record from database
        $record = FeedbackPost::where('id', '=', $id)
            ->where('hash', '=', $hash)
            ->where('closed', '=', 0)
            ->first();

        // check does we found it
        if ($record === null) {
            throw new ForbiddenException(__('The feedback request is not founded'));
        }

        // check if action is submited
        if (App::$Request->request->get('closeRequest', false)) {
            // if created by authorized user
            if ((int)$record->user_id !== 0) {
                $user = App::$User->identity();
                // button is pressed not by request creator
                if ($user === null || $user->getId() !== (int)$record->user_id) {
                    throw new ForbiddenException(__('This feedback request was created by another user'));
                }
            }

            // switch closed to 1 and make sql query
            $record->closed = 1;
            $record->save();

            // add notification and redirect
            App::$Session->getFlashBag()->add('warning', __('Feedback request now is closed!'));
            App::$Response->redirect('feedback/read/' . $id . '/' . $hash);
        }

        return App::$View->render('close');
    }

    /**
     * List feedback requests messages from authorized user
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionList()
    {
        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // check if user is authorized or throw exception
        if (!App::$User->isAuth()) {
            throw new ForbiddenException(__('Feedback listing available only for authorized users'));
        }

        // get current user object
        $user = App::$User->identity();

        // initialize query with major condition
        $query = FeedbackPost::where('user_id', '=', $user->getId());

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['feedback/list'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build records object from prepared query using page offset
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // render viewer with parameters
        return App::$View->render('list', [
            'records' => $records,
            'pagination' => $pagination,
        ]);
    }

}