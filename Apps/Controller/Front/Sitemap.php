<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\Sitemap\EntityIndexList;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;

/**
 * Class Sitemap. Display sitemap for search engines in xml format.
 * @package Apps\Controller\Front
 */
class Sitemap extends FrontAppController
{
    const EVENT_SITEMAP_LIST = 'sitemap.index';

    /**
     * Before run: set xml header and disable global html layout
     */
    public function before()
    {
        $this->layout = null;
        App::$Response->headers->set('Content-type', 'text/xml');
    }

    /**
     * List available sitemap index links
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex()
    {
        // initialize model - scan available sitemap indexes
        $model = new EntityIndexList(App::$Request->getLanguage());

        // run event - allow add any other sitemap indexes in model before render it
        App::$Event->run(static::EVENT_SITEMAP_LIST, [
            'model' => $model
        ]);

        // build information about files
        $model->make();

        return App::$View->render('index', [
            'model' => $model
        ]);
    }
}