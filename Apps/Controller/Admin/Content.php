<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Admin\Content\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;

/**
 * Class Content. Admin controller to manage & control contents
 * @package Apps\Controller\Admin
 */
class Content extends AdminController
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    // import heavy actions
    use Content\ActionIndex {
        index as actionIndex;
    }

    use Content\ActionUpdate {
        update as actionUpdate;
    }

    use Content\ActionDelete {
        delete as actionDelete;
    }

    use Content\ActionRestore {
        restore as actionRestore;
    }

    use Content\ActionClear {
        clear as actionClear;
    }

    use Content\ActionCategoryDelete {
        categoryDelete as actionCategorydelete;
    }

    use Content\ActionCategoryUpdate {
        categoryUpdate as actionCategoryupdate;
    }

    use Content\ActionGlobDelete {
        globDelete as actionGlobdelete;
    }

    use Content\ActionPublish {
        publish as actionPublish;
    }

    /**
     * Display category list
     * @return string
     * @throws SyntaxException
     */
    public function actionCategories(): ?string
    {
        return $this->view->render('category_list', [
            'categories' => ContentCategory::getSortedAll()
        ]);
    }

    /**
     * Show settings form with prepared model
     * @return string
     * @throws SyntaxException
     */
    public function actionSettings(): ?string
    {
        // init model with config array data
        $model = new FormSettings($this->getConfigs());

        // check if form is send
        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('content/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // draw response
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }
}
