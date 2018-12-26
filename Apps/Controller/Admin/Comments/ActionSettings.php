<?php

namespace Apps\Controller\Admin\Comments;

use Apps\Model\Admin\Comments\FormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\Comments
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * Comment widget global settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings()
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
        return $this->view->render('comments/settings', [
            'model' => $model
        ]);
    }
}
