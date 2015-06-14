<?php

namespace Apps\Controller\Admin;

use Extend\Core\Arch\AdminAppController;
use Apps\Model\Basic\Profile as ProfileRecords;
use Ffcms\Core\App;
use Ffcms\Core\Helper\HTML\SimplePagination;

class Profile extends AdminAppController
{
    const ITEM_PER_PAGE = 10;

    // profile list
    public function actionIndex()
    {
        // init Active Record
        $query = new ProfileRecords();

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/index'],
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