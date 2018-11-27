<?php

namespace Apps\Controller\Admin\Content;

use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionImportantChange
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionImportantChange
{

    /**
     * Change content important status from user request
     * @param string $id
     * @throws NotFoundException
     */
    public function important(string $id): void
    {
        $status = (bool)$this->request->query->get('status', 0);
        $content = \Apps\ActiveRecord\Content::find($id);
        if (!$content) {
            throw new NotFoundException(__('Content {%id%} are not exist', ['id' => $id]));
        }

        if ((bool)$content->important !== $status) {
            $content->important = $status;
            $content->save();
        }

        $this->response->redirect('content/index');
    }
}