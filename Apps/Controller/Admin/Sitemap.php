<?php

namespace Apps\Controller\Admin;

use Apps\Model\Front\Sitemap\EntityIndexList;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

class Sitemap extends AdminController
{
    const VERSION = 0.1;

    public $type = 'app';

    /**
     * Show index page - sitemap guide and info
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        $model = new EntityIndexList();
        
        return App::$View->render('index', [
            'model' => $model
        ]);
    }
}