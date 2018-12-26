<?php

namespace Apps\Controller\Admin\Profile;

use Apps\ActiveRecord\ProfileField;
use Apps\Model\Admin\Profile\FormFieldUpdate;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionFieldDelete
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionFieldDelete
{
    /**
     * Delete custom profile field
     * @param string $id
     * @return string
     * @throws ForbiddenException
     */
    public function profileFieldDelete($id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new ForbiddenException();
        }

        // check if record with $id is exist
        $record = ProfileField::find($id);
        if (!$record) {
            throw new ForbiddenException();
        }

        $model = new FormFieldUpdate($record);
        // if delete is submited - lets remove this record
        if ($model->send()) {
            $model->delete();
            $this->response->redirect('profile/fieldlist');
        }

        return $this->view->render('profile/field_delete', [
            'model' => $model
        ]);
    }
}
