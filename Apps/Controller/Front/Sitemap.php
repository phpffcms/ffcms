<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\Sitemap\EntityIndexList;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Sitemap. Display sitemap for search engines in xml format.
 * @package Apps\Controller\Front
 */
class Sitemap extends FrontAppController
{
    const EVENT_SITEMAP_LIST = 'sitemap.index';

    /**
     * Proxy forward to action "xml"
     * @return string|null
     */
    public function actionIndex(): ?string
    {
        return $this->actionXml();
    }

    /**
     * Display lazy html sitemap
     * @return string|null
     */
    public function actionHtml(): ?string
    {
        $lang = App::$Request->getLanguage();
        $files = File::listFiles(root . EntityIndexList::INDEX_PATH, ['.json'], true);
        $links = [];
        foreach ($files as $file) {
            if (!Str::contains('.' . $lang . '.', $file)) {
                continue;
            }

            $items = json_decode(File::read(EntityIndexList::INDEX_PATH . '/' . $file));
            foreach ($items as $item) {
                $links[] = $item;
            }
        }

        return $this->view->render('sitemap/html', [
            'links' => $links
        ]);
    }

    /**
     * List available sitemap index links
     * @return string
     */
    public function actionXml(): ?string
    {
        $this->response->headers->set('Content-type', 'text/xml');
        // initialize model - scan available sitemap indexes
        $model = new EntityIndexList($this->request->getLanguage());

        // run event - allow add any other sitemap indexes in model before render it
        App::$Event->run(static::EVENT_SITEMAP_LIST, [
            'model' => $model
        ]);

        // build information about files
        $model->make();

        return $this->view->render('sitemap/index', [
            'model' => $model
        ]);
    }
}
