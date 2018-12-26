<?php

namespace Apps\Controller\Admin;

use Extend\Core\Arch\AdminController;

/**
 * Class Content. Admin controller to manage & control contents
 * @package Apps\Controller\Admin
 */
class Content extends AdminController
{
    const VERSION = '1.0.1';
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

    use Content\ActionCategoryList {
        contentCategoryList as actionCategories;
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

    use Content\ActionDisplayChange {
        display as actionDisplay;
    }

    use Content\ActionImportantChange {
        important as actionImportant;
    }

    use Content\ActionSettings {
        settings as actionSettings;
    }
}
