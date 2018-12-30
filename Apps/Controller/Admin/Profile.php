<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Profile\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Profile. Admin controller of profile application.
 * @package Apps\Controller\Admin
 */
class Profile extends AdminController
{
    const VERSION = '1.0.1';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    /** Import heavy actions */
    use Profile\ActionIndex {
        index as actionIndex;
    }

    use Profile\ActionUpdate {
        profileUpdate as actionUpdate;
    }

    use Profile\ActionFieldList {
        profileFieldList as actionFieldlist;
    }

    use Profile\ActionFieldUpdate {
        profileFieldUpdate as actionFieldupdate;
    }

    use Profile\ActionFieldDelete {
        profileFieldDelete as actionFielddelete;
    }

    use Profile\ActionSettings {
        settings as actionSettings;
    }
}
