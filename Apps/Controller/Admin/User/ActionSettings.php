<?php

namespace Apps\Controller\Admin\User;


use Apps\Model\Admin\User\FormUserSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * User app settings action
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings(): ?string
    {
        // load model and pass property's as argument
        $model = new FormUserSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('user/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('user/settings', [
            'model' => $model
        ]);
    }
}