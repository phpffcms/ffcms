<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\Model\Front\Profile\FormIgnoreAdd;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Response;
use Ffcms\Core\Network\Request;

/**
 * Trait ActionIgnore
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionIgnore
{
    private $_blockPerPage;

    /**
     * Show users in blacklist and allow add new users
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function ignore()
    {
        // check if not auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object and init model of it
        $user = App::$User->identity();
        $model = new FormIgnoreAdd($user);

        // set user id from ?id= get param if form not sended
        if (!$model->send()) {
            $uid = (int)$this->request->query->get('id');
            if ($uid > 0) {
                $model->id = $uid;
            }
        }

        // sended new block add?
        if ($model->send() && $model->validate()) {
            if ($model->save()) {
                App::$Session->getFlashBag()->add('success', __('User was successful blocked!'));
            } else {
                App::$Session->getFlashBag()->add('error', __('This user is always in ignore list'));
            }
        }

        // get blocked users
        $query = Blacklist::where('user_id', '=', $user->getId());

        $page = (int)$this->request->query->get('page');
        $offset = $page * $this->_blockPerPage;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/ignore'],
            'page' => $page,
            'step' => $this->_blockPerPage,
            'total' => $query->count()
        ]);

        // get records as object
        $records = $query->with(['targetUser', 'targetUser.profile'])
            ->skip($offset)
            ->take($this->_blockPerPage)
            ->get();

        // render output view
        return $this->view->render('ignore', [
            'records' => $records,
            'model' => $model,
            'pagination' => $pagination
        ]);
    }
}