<?php

namespace Apps\Controller\Admin\Feedback;

use Apps\Model\Admin\Feedback\FormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\Feedback
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings(): ?string
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
        return $this->view->render('feedback/settings', [
            'model' => $model
        ]);
    }
}
