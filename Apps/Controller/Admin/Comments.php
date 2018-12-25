<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\CommentPost;
use Apps\Model\Admin\Comments\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NotFoundException;

/**
 * Class Comments. Admin controller for management user comments.
 * This class provide general admin implementation of control for user comments and its settings.
 * @package Apps\Controller\Admin
 */
class Comments extends AdminController
{
    const VERSION = '1.0.1';
    const ITEM_PER_PAGE = 10;

    const TYPE_COMMENT = 'comment';
    const TYPE_ANSWER = 'answer';

    public $type = 'widget';

    // heavy actions import
    use Comments\ActionIndex {
        index as actionIndex;
    }

    use Comments\ActionEdit {
        edit as actionEdit;
    }

    use Comments\ActionDelete {
        delete as actionDelete;
    }

    use Comments\ActionPublish {
        publish as actionPublish;
    }

    use Comments\ActionAnswerList {
        answerList as actionAnswerlist;
    }

    use Comments\ActionRead {
        read as actionRead;
    }

    use Comments\ActionDisplay {
        display as actionDisplay;
    }

    use Comments\ActionSettings {
        settings as actionSettings;
    }
}
