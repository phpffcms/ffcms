<?php

namespace Apps\Controller\Admin;

use Extend\Core\Arch\AdminController;
use Apps\Model\Basic\User as UserRecords;
use Ffcms\Core\App;
use Ffcms\Core\Helper\HTML\SimplePagination;


class User extends AdminController
{
    const ITEM_PER_PAGE = 10;

    // list users
    public function actionIndex()
    {
        // init Active Record
        $query = new UserRecords();

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['user/index'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $query->count()
        ]);

        // build listing objects
        $records = $query->orderBy('id', 'desc')->skip($offset)->take(self::ITEM_PER_PAGE)->get();

        // display viewer
        $this->response = App::$View->render('index', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }
}