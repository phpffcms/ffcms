<?php

namespace Apps\Controller\Admin\Content;

use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDisplayChange
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionDisplayChange
{
    /**
     * Change content display status on user request
     * @param $id
     * @throws NotFoundException
     * @return void
     */
    public function display(string $id): void
    {
        $status = (bool)$this->request->query->get('status', 0);
        $content = \Apps\ActiveRecord\Content::find($id);
        if (!$content) {
            throw new NotFoundException(__('Content {%id%} are not exist', ['id' => $id]));
        }
        // make update query if status is different than required
        if ((bool)$content->display !== $status) {
            $content->display = $status;
            $content->save();
        }

        $this->response->redirect('content/index');
    }
}
