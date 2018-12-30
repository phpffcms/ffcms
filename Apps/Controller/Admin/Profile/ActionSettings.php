<?php

namespace Apps\Controller\Admin\Profile;


use Apps\Model\Admin\Profile\FormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * Profile global settings action
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings(): ?string
    {
        $model = new FormSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('profile/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        return $this->view->render('profile/settings', [
            'model' => $model
        ]);
    }
}