<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormCommentDelete;
use Apps\Model\Admin\Comments\FormCommentModerate;
use Apps\Model\Admin\Comments\FormCommentUpdate;
use Apps\Model\Admin\Comments\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;

/**
 * Class Comments. Admin controller for management user comments.
 * This class provide general admin implementation of control for user comments and its settings.
 * @package Apps\Controller\Admin
 */
class Comments extends AdminController
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    const TYPE_COMMENT = 'comment';
    const TYPE_ANSWER = 'answer';

    public $type = 'widget';

    /**
     * List user comments with pagination
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // initialize active record model
        $query = new CommentPost();

        // make pagination
        $pagination = new SimplePagination([
            'url' => ['comments/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // get result as active records object with offset
        $records = $query->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * List comment - read comment and list answers
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRead($id)
    {
        // find object in active record model
        $record = CommentPost::find($id);
        if ($record === null || $record === false) {
            throw new NotFoundException(__('Comment is not founded'));
        }

        // render response
        return $this->view->render('comment_read', [
            'record' => $record
        ]);
    }

    /**
     * Commentaries and answers edit action
     * @param string $type
     * @param int $id
     * @throws NotFoundException
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionEdit($type, $id)
    {
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
        if ($record === null || $record === false) {
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
        return $this->view->render('edit', [
            'model' => $model
        ]);
    }

    /**
     * Delete comments and answers single or multiply items
     * @param string $type
     * @param int $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionDelete($type, $id = 0)
    {
        // sounds like a multiply delete definition
        if ($id === 0 || (int)$id < 1) {
            $ids = $this->request->query->get('selected');
            if (!Any::isArray($ids) || !Arr::onlyNumericValues($ids)) {
                throw new NotFoundException('Bad conditions');
            }

            $id = $ids;
        } else {
            $id = [$id];
        }

        // prepare query to db
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
        if ($query === null || $query->count() < 1) {
            throw new NotFoundException(__('No comments found for this condition'));
        }

        // initialize model
        $model = new FormCommentDelete($query, $type);

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Comments or answers are successful deleted!'));
            $this->response->redirect('comments/' . ($type === 'answer' ? 'answerlist' : 'index'));
        }

        // render view
        return $this->view->render('delete', [
            'model' => $model
        ]);
    }

    /**
     * Moderate guest comments and answer - make it publish
     * @param string $type
     * @param int $id
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionPublish($type, $id = 0)
    {
        // check if it multiple accept ids
        if ($id === 0 || (int)$id < 1) {
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

    /**
     * List answers
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionAnswerlist()
    {
        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // initialize ar answers model
        $query = new CommentAnswer();

        // build pagination list
        $pagination = new SimplePagination([
            'url' => ['comments/answerlist'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // get result as active records object with offset
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // render output view
        return $this->view->render('answer_list', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * Comment widget global settings
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings()
    {
        // initialize settings model
        $model = new FormSettings($this->getConfigs());

        // check if form is send
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('comments/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }
}
