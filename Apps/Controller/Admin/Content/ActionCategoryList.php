<?php

namespace Apps\Controller\Admin\Content;

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Class ActionCategoryList
 * @package Apps\Controller\Admin\Content
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionCategoryList
{
    /**
     * Display category list
     * @return string|null
     */
    public function contentCategoryList(): ?string
    {
        return $this->view->render('content/category_list', [
            'categories' => ContentCategory::getSortedAll()
        ]);
    }
}
