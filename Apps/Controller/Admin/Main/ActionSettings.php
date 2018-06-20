<?php

namespace Apps\Controller\Admin\Main;

use Apps\Model\Admin\Main\FormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSettings
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSettings
{
    /**
     * Manage settings in web
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function settings()
    {
        // init settings model and process post send
        $model = new FormSettings(true);
        if ($model->send()) {
            if ($model->validate()) {
                if ($model->makeSave()) {
                    // show message about successful save and take system some time ;)
                    return $this->view->render('main/settings_save');
                } else {
                    App::$Session->getFlashBag()->add('error', __('Configuration file is not writable! Check /Private/Config/ dir and files'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Validation of form data is failed!'));
            }
        }

        // render output view
        return $this->view->render('main/settings', [
            'model' => $model
        ]);
    }
}
