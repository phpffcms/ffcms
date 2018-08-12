<?php

namespace Apps\Controller\Admin\Profile;

use Apps\Model\Admin\Profile\FormFieldUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\ProfileField;

/**
 * Trait ActionFieldUpdate
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionFieldUpdate
{

    /**
     * Add new or edit exist additional fields for user profiles
     * @param string|null $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function profileFieldUpdate($id = null)
    {
        // get current record or new and init form DI
        $record = ProfileField::findOrNew($id);
        $model = new FormFieldUpdate($record);

        // check if form is submitted
        if ($model->send() && $model->validate()) {
            $model->save();
            if ($record->id) {
                $this->response->redirect('profile/fieldlist');
            }
            App::$Session->getFlashBag()->add('success', __('Profile field was successful updated'));
        }

        return $this->view->render('profile/field_update', [
            'model' => $model
        ]);
    }
}