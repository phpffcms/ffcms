<?php

namespace Apps\Controller\Admin\Profile;

use Apps\ActiveRecord\Profile;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionIndex
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List all user profiles
     * @return null|string
     */
    public function index(): ?string
    {
        // init Active Record
        $query = Profile::with(['user']);

        // set current page and offset
        $page = (int)$this->request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // count total items count for pagination builder
        $total = $query->count();

        // build listing objects
        $records = $query->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // display viewer
        return $this->view->render('profile/index', [
            'records' => $records,
            'pagination' => [
                'url' => ['profile/index'],
                'page' => $page,
                'step' => self::ITEM_PER_PAGE,
                'total' => $total
            ]
        ]);
    }
}