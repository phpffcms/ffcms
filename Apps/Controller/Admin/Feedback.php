<?php


namespace Apps\Controller\Admin;

use Apps\Model\Admin\Feedback\FormSettings;
use Extend\Core\Arch\AdminController as Controller;
use Ffcms\Core\App;

/**
 * Class Feedback. Control and manage feedback request and answers.
 * @package Apps\Controller\Admin
 */
class Feedback extends Controller
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    // import heavy actions
    use Feedback\ActionIndex {
        index as actionIndex;
    }

    use Feedback\ActionRead {
        read as actionRead;
    }

    use Feedback\ActionUpdate {
        update as actionUpdate;
    }

    use Feedback\ActionTurn {
        turn as actionTurn;
    }

    use Feedback\ActionDelete {
        delete as actionDelete;
    }

    /**
     * Settings of feedback application
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings(): ?string
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
            'model' => $model
        ]);
    }
}
