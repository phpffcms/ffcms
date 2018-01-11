<?php

namespace Apps\Controller\Front\Profile;

use Apps\Model\Front\Profile\FormUserSearch;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Response;
use Ffcms\Core\Network\Request;

/**
 * Trait ActionSearch
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs()
 */
trait ActionSearch
{
    /**
     * Search users
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function search(): ?string
    {
        // create model object
        $model = new FormUserSearch();
        $model->setSubmitMethod('GET');

        // get app configs
        $cfgs = $this->getConfigs();

        $records = null;
        $pagination = null;
        // check if request is sended
        if ($model->send() && $model->validate()) {
            // get records from db
            $records = ProfileRecords::where('nick', 'like', '%' . $model->query . '%');
            $page = (int)$this->request->query->get('page');
            $userPerPage = (int)$cfgs['usersOnPage'];
            if ($userPerPage < 1) {
                $userPerPage = 1;
            }

            $offset = $page * $userPerPage;
            // build pagination
            $pagination = new SimplePagination([
                'url' => ['profile/search', null, null, [$model->getFormName().'[query]' => $model->query, $model->getFormName().'[submit]' => true]],
                'page' => $page,
                'step' => $userPerPage,
                'total' => $records->count()
            ]);
            // make query finally
            $records = $records->skip($offset)
                ->take($userPerPage)
                ->get();
        }

        // display response
        return $this->view->render('search', [
            'model' => $model,
            'records' => $records,
            'pagination' => $pagination,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }
}
