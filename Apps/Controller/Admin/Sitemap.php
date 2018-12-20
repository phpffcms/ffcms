<?php

namespace Apps\Controller\Admin;

use Apps\Model\Front\Sitemap\EntityIndexList;
use Extend\Core\Arch\AdminController;

/**
 * Class Sitemap. Admin controller of sitemap app
 * @package Apps\Controller\Admin
 */
class Sitemap extends AdminController
{
    const VERSION = '1.0.1';

    public $type = 'app';

    /**
     * Show index page - sitemap guide and info
     * @return string
     */
    public function actionIndex()
    {
        $model = new EntityIndexList();
        
        return $this->view->render('sitemap/index', [
            'model' => $model
        ]);
    }
}
