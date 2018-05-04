<?php

namespace Apps\Controller\Front\Content;

use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentRecord;

/**
 * Trait ActionMy
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionMy
{

    /**
     * Show user added content list
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function my(): ?string
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException(__('Only authorized users can manage content'));
        }

        // check if user add enabled
        $configs = $this->getConfigs();
        /**if (!(bool)$configs['userAdd']) {
            throw new NotFoundException(__('User add is disabled'));
        }*/

        // prepare query
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * 10;
        $query = ContentRecord::where('author_id', App::$User->identity()->getId());

        // calc total count before limit applied
        $totalCount = $query->count();

        // build records object
        $records = $query->skip($offset)->take(10)->orderBy('id', 'DESC')->get();

        // render output view
        return $this->view->render('content/my', [
            'records' => $records,
            'pagination' => [
                'step' => 10,
                'total' => $totalCount,
                'page' => $page
            ]
        ]);
    }
}
