<?php

namespace Apps\Controller\Admin\User;

use Apps\Model\Admin\User\FormUserDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDelete
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDelete
{
    /**
     * Delete user row from database
     * @param string|null $id
     * @return string|null
     * @throws \Exception
     */
    public function delete(?string $id = null): ?string
    {
        // check if id is passed or get data from GET as array ids
        if (!Any::isInt($id) || $id < 1) {
            $ids = $this->request->query->get('selected');
            if (!Any::isArray($ids) || !Arr::onlyNumericValues($ids)) {
                throw new NotFoundException('bad conditions');
            }
            $id = $ids;
        } else {
            $id = [$id];
        }

        // initialize delete model
        $model = new FormUserDelete($id);

        // check if users is found
        if ($model->users === null) {
            throw new NotFoundException(__('Users are not found'));
        }

        // check if delete is submited
        if ($model->send() && $model->validate()) {
            $model->delete();
            App::$Session->getFlashBag()->add('success', __('Users and them data are successful removed'));
            $this->response->redirect('user/index');
        }

        // set view response
        return $this->view->render('user/user_delete', [
            'model' => $model
        ]);
    }
}
