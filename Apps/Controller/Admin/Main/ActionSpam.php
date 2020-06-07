<?php


namespace Apps\Controller\Admin\Main;


use Apps\ActiveRecord\Spam;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionFilter
 * @package Apps\Controller\Admin\Main
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionSpam
{
    public static $itemPerPage = 100;

    /**
     * Show filter logs
     * @return string|null
     */
    public function filter(): ?string
    {
        $page = (int)$this->request->query->get('page');
        $offset = $page * static::$itemPerPage;

        $records = Spam::orderBy('counter', 'DESC')
            ->take(static::$itemPerPage)
            ->offset($offset)
            ->get();

        return $this->view->render('main/spam', [
            'records' => $records,
            'pagination' => [
                'url' => ['main/spam'],
                'page' => $page,
                'step' => self::$itemPerPage,
                'total' => Spam::count()
            ]
        ]);
    }

}