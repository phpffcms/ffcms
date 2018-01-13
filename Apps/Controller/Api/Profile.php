<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;

/**
 * Class Profile. Api controller provide ajax/json for user profile features
 * @package Apps\Controller\Api
 */
class Profile extends ApiController
{
    const ITEM_PER_PAGE = 10;
    const ANSWER_DELAY = 60; // in seconds

    const MSG_USER_LIST = 10;
    const MSG_TEXT_LIST = 20;

    // include actions from traits
    use Profile\ActionWallAnswerCount {
        wallAnswerCount as actionWallanswercount;
    }

    use Profile\ActionShowWallAnswers {
        showWallAnswers as actionShowwallanswers;
    }

    use Profile\ActionSendWallAnswer {
        sendWallAnswer as actionSendwallanswer;
    }

    use Profile\ActionDeleteAnswerOwner {
        deleteAnswerOwner as actionDeleteanswerowner;
    }

    use Profile\ActionListMessageDialog {
        listMessageDialog as actionListmessagedialog;
    }

    use Profile\ActionNotifications {
        notifications as actionNotifications;
    }

    use Profile\ActionMessageList {
        messageList as actionMessageList;
    }

    use Profile\ActionMessageSend {
        messageSend as actionMessagesend;
    }

    use Profile\ActionChangeRating {
        changeRating as actionChangerating;
    }
}
