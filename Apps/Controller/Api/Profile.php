<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;

class Profile extends ApiController
{
    const ITEM_PER_PAGE = 10;
    const ANSWER_DELAY = 60; // in seconds

    /**
     * Get wall answer's count by post-ids list
     * @param int $postIds
     * @throws JsonException
     */
    public function actionWallanswercount($postIds)
    {
        // set header
        $this->setJsonHeader();
        // check what we got
        if ($postIds === null || String::length($postIds) < 1) {
            throw new JsonException('Wrong input count');
        }

        $list = explode(',', $postIds);
        $itemCount = count($list);
        // empty or is biggest then limit?
        if ($itemCount < 1 || $itemCount > self::ITEM_PER_PAGE) {
            throw new JsonException('Wrong input count');
        }

        // prepare response
        $response = [];
        foreach ($list as $post) {
            $response[$post] = WallAnswer::where('post_id', '=', $post)->count();
        }

        // display json data
        $this->response = json_encode([
            'status' => 1,
            'data' => $response
        ]);
    }

    /**
     * Show all answers for this post id
     * @param int $postId
     * @throws JsonException
     */
    public function actionShowwallanswers($postId)
    {
        // check input post id num
        if (!Object::isLikeInt($postId) || $postId < 1) {
            throw new JsonException('Wrong input data');
        }

        // try to find this post
        $object = WallPost::find($postId);

        if ($object === null || $object === false) {
            throw new JsonException('Wrong input data');
        }

        $response = [];

        foreach ($object->getAnswer()->orderBy('id', 'DESC')->get() as $answer) {
            // get user object and profile
            $user = $answer->getUser();
            $profile = $user->getProfile();
            // check if user exist
            if ($user === null || $user->id < 1) {
                continue;
            }
            // generate response array
            $response[] = [
                'answer_id' => $answer->id,
                'user_id' => $answer->user_id,
                'user_nick' => $profile->nick === null ? __('No name') : App::$Security->strip_tags($profile->nick),
                'user_avatar' => $profile->getAvatarUrl('small'),
                'answer_message' => App::$Security->strip_tags($answer->message),
                'answer_date' => Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_SECONDS)
            ];
        }

        $this->response = json_encode(['status' => 1, 'data' => $response]);
    }

    /**
     * Add new post answer from AJAX post
     * @param int $postId
     * @throws JsonException
     */
    public function actionSendwallanswer($postId)
    {
        // not auth? what are you doing there? ;)
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }

        // no post id? wtf you doing man!
        if (!Object::isLikeInt($postId) || $postId < 1) {
            throw new JsonException('Wrong input data');
        }

        // get message from post and validate minlength
        $message = App::$Request->get('message');
        if (!Object::isString($message) || String::length($message) < 3) {
            throw new JsonException('Wrong input data');
        }

        // try to find this post
        $wallCount = WallPost::where('id', '=', $postId)->count();
        if ($wallCount < 1) {
            throw new JsonException('Wrong input data');
        }

        // check delay between user last post and current
        $lastAnswer = WallAnswer::where('user_id', '=', App::$User->identity()->getId())->orderBy('created_at', 'DESC')->first();
        if (null !== $lastAnswer && false !== $lastAnswer) {
            $now = time();
            $answerTime = Date::convertToTimestamp($lastAnswer->created_at);
            // hmm, maybe past less then delay required?
            if ($now - self::ANSWER_DELAY < $answerTime) {
                throw new JsonException('Delay between answers not pass');
            }
        }

        // make new row ;)
        $answers = new WallAnswer();
        $answers->post_id = $postId;
        $answers->user_id = App::$User->identity()->getId();
        $answers->message = App::$Security->strip_tags($message);
        $answers->save();

        // send "ok" response
        $this->setJsonHeader();
        $this->response = json_encode(['status' => 1, 'message' => 'ok']);
    }

    public function actionDeleteanswerowner($answerId)
    {
        $this->setJsonHeader();
        // hello script kiddy, you must be auth ;)
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }
        // answer id must be an unsigned integer
        if (!Object::isLikeInt($answerId) || $answerId < 1) {
            throw new JsonException('Wrong input data');
        }

        $findAnswer = WallAnswer::find($answerId);

        // check if this answer id exist
        if (null === $findAnswer || false === $findAnswer) {
            throw new JsonException('Wrong input data');
        }

        // get current viewer
        $viewer = App::$User->identity();
        // get post info
        $postInfo = $findAnswer->getWallPost();

        // if not a target user of answer and not answer owner - lets throw exception
        if($postInfo->target_id !== $viewer->id && $findAnswer->user_id !== $viewer->id) {
            throw new JsonException('Access declined!');
        }

        // all is ok, lets remove this answer ;)
        $findAnswer->delete();

        $this->response = json_encode([
           'status' => 1,
            'message' => 'ok'
        ]);
    }
}