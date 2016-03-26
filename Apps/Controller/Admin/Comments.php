<?php

namespace Apps\Controller\Admin;


use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Arr;

/**
 * Class Comments. Admin controller for management user comments.
 * This class provide general admin implementation of control for user comments and its settings.
 * @package Apps\Controller\Admin
 */
class Comments extends AdminController
{
    const VERSION = 0.1;
    const ITEM_PER_PAGE = 10;

    const TYPE_COMMENT = 'comment';
    const TYPE_ANSWER = 'answer';

    public $type = 'widget';

    /**
     * List user comments with pagination
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // set current page and offset
        $page = (int)App::$Request->query->get('page');
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
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // render output view
        return App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * List comment - read comment and list answers
     * @param int $id
     * @return string
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
        return App::$View->render('comment_read', [
            'record' => $record
        ]);
    }

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
        if ($record === null || $record->count() != 1) {
            throw new NotFoundException(__('Comment is not founded'));
        }



    }

    public function actionAnswerlist()
    {

    }

    /**
     * Comment widget global settings
     * @return string
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
                App::$Response->redirect('comments/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return App::$View->render('settings', [
            'model' => $model
        ]);
    }




}