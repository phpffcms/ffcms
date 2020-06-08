<?php

namespace Apps\Controller\Admin\Main;

use Ffcms\Core\App;
use Apps\ActiveRecord\Ban;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionBanDelete
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionBanDelete
{
    /**
     * Ban delete action
     * @param string|int $id 
     * @return null|string 
     * @throws NotFoundException 
     */
    public function delete($id): ?string
    {
        $record = Ban::find($id);

        if (!$record) {
            throw new NotFoundException(__('Record not found'));
        }

        $record->delete();

        App::$Session->getFlashBag()->add('success', __('Ban record was removed'));

        $this->response->redirect('main/ban');
        return null;
    }
}