<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;

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

    // heavy actions import
    use Comments\ActionIndex {
        index as actionIndex;
    }

    use Comments\ActionEdit {
        edit as actionEdit;
    }

    use Comments\ActionDelete {
        delete as actionDelete;
    }

    use Comments\ActionPublish {
        publish as actionPublish;
    }

    use Comments\ActionAnswerList {
        answerList as actionAnswerlist;
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
        return $this->view->render('comment_read', [
            'record' => $record
        ]);
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
