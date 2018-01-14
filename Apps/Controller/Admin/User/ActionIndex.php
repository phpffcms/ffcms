<?php

namespace Apps\Controller\Admin\User;

use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\User as UserRecord;

/**
 * Class ActionIndex
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List all users as table
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function index(): ?string
    {
        // init Active Record user object relation
        $record = new UserRecord();

        // set current page num and offset
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * self::ITEM_PER_PAGE;

        // build pagination object
        $pagination = new SimplePagination([
            'url' => ['user/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $record->count()
        ]);

        // build listing objects
        $records = $record->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // display viewer
        return $this->view->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }
}
