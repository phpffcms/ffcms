<?php

namespace Apps\Controller\Admin\User;

use Apps\ActiveRecord\User as UserRecord;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

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
     * @return string|null
     */
    public function index(): ?string
    {
        // init Active Record user object relation
        $record = new UserRecord();

        // check if search query passed
        $query = $this->request->query->get('search', null);
        if ($query && Any::isStr($query) && Str::length($query) > 1) {
            $record = $record->where(function ($db) use ($query) {
                $db->where('login', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            });
        }

        // set current page num and offset
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * self::ITEM_PER_PAGE;

        // prepare pagination data
        $pagination = [
            'url' => ['user/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $record->count()
        ];

        // build listing objects
        $records = $record->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        // display viewer
        return $this->view->render('user/index', [
            'records' => $records,
            'pagination' => $pagination,
            'search' => $query
        ]);
    }
}
