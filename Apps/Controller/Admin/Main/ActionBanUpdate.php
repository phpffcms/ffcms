<?php

namespace Apps\Controller\Admin\Main;

use Apps\ActiveRecord\Ban;
use Apps\Model\Admin\Main\FormBanUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Core\Helper\Type\Str;


/**
 * Trait ActionBanUpdate
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionBanUpdate 
{
    /**
     * Update ban record
     * @param int|null $id 
     * @return null|string  
     * @throws Exception 
     */
    public function update($id = null): ?string
    {
        $ip = (string)$this->request->get('ip');
        $userId = (int)$this->request->get('user');

        $record = Ban::findOrNew($id);

        $model = new FormBanUpdate($record);
        if (Str::length($ip) > 0) {
            $model->ip = $ip;
        }

        if ($userId > 0) {
            $model->userId = $userId;
        }

        if ($model->send() && $model->validate()) {
            $model->save();
            App::$Session->getFlashBag()->add('success', __('Ban row is successful saved'));
            $this->response->redirect('main/ban');
        }

        return $this->view->render('main/ban_update', [
            'model' => $model
        ]);
    }
}