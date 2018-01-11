<?php

namespace Apps\Controller\Front\Content;

use Apps\Model\Front\Content\FormNarrowContentUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentRecord;

/**
 * Trait ActionUpdate
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionUpdate
{
    /**
     * Update personal content items or add new content item
     * @param int|null $id
     * @return null|string
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function update($id = null)
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException(__('Only authorized users can add content'));
        }

        // check if user add enabled
        $configs = $this->getConfigs();
        if (!(bool)$configs['userAdd']) {
            throw new NotFoundException(__('User add is disabled'));
        }

        // find record in db
        $record = ContentRecord::findOrNew($id);
        $new = $record->id === null;

        // reject edit published items and items from other authors
        if (($new === false && (int)$record->author_id !== App::$User->identity()->getId()) || (int)$record->display === 1) {
            throw new ForbiddenException(__('You have no permissions to edit this content'));
        }

        // initialize model
        $model = new FormNarrowContentUpdate($record, $configs);
        if ($model->send() && $model->validate()) {
            $model->make();
            // if is new - make redirect to listing & add notify
            if ($new === true) {
                App::$Session->getFlashBag()->add('success', __('Content successfully added'));
                $this->response->redirect('content/my');
            } else {
                App::$Session->getFlashBag()->add('success', __('Content successfully updated'));
            }
        }

        // render view output
        return $this->view->render('update', [
            'model' => $model,
            'configs' => $configs
        ]);
    }
}
