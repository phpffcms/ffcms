<?php

namespace Apps\Controller\Admin;

use Extend\Core\Arch\AdminController as Controller;

/**
 * Class Feedback. Control and manage feedback request and answers.
 * @package Apps\Controller\Admin
 */
class Feedback extends Controller
{
    const VERSION = '1.0.1';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    // import heavy actions
    use Feedback\ActionIndex {
        index as actionIndex;
    }

    use Feedback\ActionRead {
        read as actionRead;
    }

    use Feedback\ActionUpdate {
        update as actionUpdate;
    }

    use Feedback\ActionTurn {
        turn as actionTurn;
    }

    use Feedback\ActionDelete {
        delete as actionDelete;
    }

    use Feedback\ActionSettings {
        settings as actionSettings;
    }
}
