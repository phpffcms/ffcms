<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Apps\Model\Front\Profile\EntityAddNotification;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Core\Helper\Text;

/**
 * Trait ActionSendWallAnswer
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionSendWallAnswer
{
    /**
     * Add new post answer from AJAX post
     * @param string $postId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function sendWallAnswer(string $postId): ?string
    {
        $this->setJsonHeader();

        // not auth? what are you doing there? ;)
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        // no post id? wtf you doing man!
        if (!Any::isInt($postId) || $postId < 1) {
            throw new NativeException('Wrong input data');
        }

        // get current(sender) user object
        $viewer = App::$User->identity();

        // get message from post and validate minlength
        $message = $this->request->get('message');
        $message = App::$Security->strip_tags($message);
        if (!Any::isStr($message) || Str::length($message) < 3) {
            throw new ForbiddenException('Wrong input data');
        }

        // try to find this post
        $wallPost = WallPost::where('id', '=', $postId);
        if ($wallPost->count() < 1) {
            throw new NativeException('Wrong input data');
        }

        $wallRow = $wallPost->first();
        $targetId = $wallRow->target_id;
        // check if in blacklist
        if (!Blacklist::check($viewer->id, $targetId)) {
            throw new ForbiddenException('User is blocked!');
        }

        // check delay between user last post and current
        $lastAnswer = WallAnswer::where('user_id', '=', App::$User->identity()->getId())
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!$lastAnswer) {
            $now = time();
            $answerTime = Date::convertToTimestamp($lastAnswer->created_at);
            $cfgs = \Apps\ActiveRecord\App::getConfigs('app', 'Profile');
            // hmm, maybe past less then delay required?
            if ($now - (int)$cfgs['delayBetweenPost'] < $answerTime) {
                throw new ForbiddenException('Delay between answers not pass');
            }
        }

        // make new row ;)
        $answers = new WallAnswer();
        $answers->post_id = $postId;
        $answers->user_id = $viewer->id;
        $answers->message = $message;
        $answers->save();

        // add notification for target user
        if ($viewer->id !== $targetId) {
            $notify = new EntityAddNotification($targetId);
            $notify->add('/profile/show/' . $targetId . '#wall-post-' . $wallRow->id, EntityAddNotification::MSG_ADD_WALLANSWER, [
                'snippet' => Text::snippet($message, 50),
                'post' => $wallRow->message
            ]);
        }

        return json_encode(['status' => 1, 'message' => 'ok']);
    }
}
