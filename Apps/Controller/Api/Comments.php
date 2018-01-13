<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;

/**
 * Class Comments. View and add comments and answers via json based ajax query's
 * @package Apps\Controller\Api
 */
class Comments extends ApiController
{

    // import action traits
    use Comments\ActionAdd {
        add as actionAdd;
    }

    use Comments\ActionList {
        aList as actionList;
    }

    use Comments\ActionShowAnswer {
        showAnswers as actionShowanswers;
    }

    use Comments\ActionCount {
        cnt as actionCount;
    }
}
