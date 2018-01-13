<?php


namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Message;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionMessageSend
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader
 */
trait ActionMessageSend
{

    /**
     * Send message via AJAX
     * @param string $targetId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     */
    public function messageSend(string $targetId): ?string
    {
        if (!Any::isInt($targetId) || $targetId < 1) {
            throw new NativeException('Bad target id format');
        }

        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        // get current user object and check in blacklist
        $user = App::$User->identity();
        if (!Blacklist::check($user->id, $targetId)) {
            throw new ForbiddenException('In blacklist');
        }

        // check input params
        $msg = App::$Security->strip_tags($this->request->get('message'));
        if (!Any::isInt($targetId) || $targetId < 1 || Str::length($msg) < 1) {
            throw new NativeException('Wrong input data');
        }

        $this->setJsonHeader();

        // try to save message
        $message = new Message();
        $message->target_id = $targetId;
        $message->sender_id = $user->id;
        $message->message = $msg;
        $message->save();

        return json_encode(['status' => 1]);
    }
}
