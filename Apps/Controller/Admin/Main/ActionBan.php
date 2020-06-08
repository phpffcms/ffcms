<?php

namespace Apps\Controller\Admin\Main;

use Apps\ActiveRecord\Ban;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionBan
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionBan 
{
    public static $itemPerPage = 100;

    public function ban(): ?string
    {
        $page = (int) $this->request->query->get('page');
        $offset = $page * static::$itemPerPage;

        $records = Ban::orderBy('id', 'DESC')
            ->take(static::$itemPerPage)
            ->offset($offset)
            ->get();

        return $this->view->render('main/ban', [
            'records' => $records,
            'pagination' => [
                'url' => ['main/ban'],
                'page' => $page,
                'step' => self::$itemPerPage,
                'total' => Ban::count()
            ]
        ]);
    }
}