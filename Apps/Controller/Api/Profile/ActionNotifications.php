<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\Message;
use Apps\ActiveRecord\UserNotification;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionNotifications
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionNotifications
{
    /**
     * Get user p.m and notifications count
     * @return string
     * @throws ForbiddenException
     */
    public function notifications(): ?string
    {
        // check if authed
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        $this->setJsonHeader();

        // get user object
        $user = App::$User->identity();

        // get messages count
        $messagesCount = Message::where('target_id', '=', $user->id)
            ->where('readed', '=', 0)->count();

        // get notifications count
        $notificationsCount = UserNotification::where('user_id', '=', $user->id)
            ->where('readed', '=', 0)->count();

        // render json output
        return json_encode([
            'status' => 1,
            'notify' => $notificationsCount,
            'messages' => $messagesCount,
            'summary' => $notificationsCount + $messagesCount
        ]);
    }
}
