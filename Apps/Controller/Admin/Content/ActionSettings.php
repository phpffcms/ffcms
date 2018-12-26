<?php

namespace Apps\Controller\Admin\Content;


use Apps\Model\Admin\Content\FormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * Show graphical settings configurator interface
     * @return null|string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings(): ?string
    {
        // init model with config array data
        $model = new FormSettings($this->getConfigs());

        // check if form is submited
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('content/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render response
        return $this->view->render('content/settings', [
            'model' => $model
        ]);
    }
}