<?php

namespace Apps\Controller\Front\Content;


use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionComments
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionComments
{

    /**
     * Redirect to full news url and show comments
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function comments(string $id): void
    {
        $content = \Apps\ActiveRecord\Content::find($id);
        if (!$content) {
            throw new NotFoundException(__('Content not found'));
        }

        // build full path
        $path = '/content/read/' . $content->getPath() . '#comments-list';

        // redirect to comment. todo: load all comments and scroll to target by id
        $this->response->redirect($path);
    }
}