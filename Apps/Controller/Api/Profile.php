<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\Message;
use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Illuminate\Database\Capsule\Manager as Capsule;

class Profile extends ApiController
{
    const ITEM_PER_PAGE = 10;
    const ANSWER_DELAY = 60; // in seconds

    const MSG_USER_LIST = 10;
    const MSG_TEXT_LIST = 20;

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

    /**
     * Delete answer by answer owner or wall owner
     * @param $answerId
     * @throws JsonException
     */
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

    /**
     * Load user dialog list based on offset
     * @param int $offset
     * @param int $new
     * @throws JsonException
     */
    public function actionListmessagedialog($offset = 0, $new = 0)
    {
        // check is user auth
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }
        $this->setJsonHeader();

        // check is offset is int
        if ($offset !== 0 && !Object::isLikeInt($offset)) {
            $offset = 0;
        }
        ++$offset;

        // get user person
        $user = App::$User->identity();

        $records = Message::select('*', Capsule::raw('max(created_at) as cmax'), Capsule::raw('min(readed) as tread'))
            ->where('target_id', '=', $user->id)
            ->orWhere('sender_id', '=', $user->id)
            ->orderBy('cmax', 'DESC')
            ->groupBy(['target_id', 'sender_id']) // group by ignore orderBy ... make some shit
            ->take($offset * self::MSG_USER_LIST)
            ->get();

        $userList = [];
        $unreadList = [];

        if (Object::isLikeInt($new) && $new > 0 && App::$User->isExist($new)) {
            $userList[] = $new;
        }
        // there is 2 way of messages: me->user; user->me, try to parse it
        foreach ($records as $row) {
            // target is not myself? then i'm - sender (remote user is target: my->to_user)
            if ($row->target_id !== $user->id) {
                $userList[] = $row->target_id;
            }

            // sender is not myself? then i'm - target (remote user is sender user->to_me)
            if ($row->sender_id !== $user->id) {
                $userList[] = $row->sender_id;
                if ($row->tread === 0) {
                    $unreadList[] = $row->sender_id;
                }
            }
        }

        // store only unique users in dialog
        $userList = array_unique($userList, SORT_NUMERIC);
        // generate json response based on userList and unreadList
        $response = [];
        foreach ($userList as $user_id) {
            $identity = App::$User->identity($user_id);
            if (null === $identity) {
                continue;
            }

            $response[] = [
                'user_id' => $user_id,
                'user_nick' => $identity->getProfile()->nick === null ? App::$Translate->get('Profile', 'No name') :
                    App::$Security->strip_tags($identity->getProfile()->nick),
                'user_avatar' => $identity->getProfile()->getAvatarUrl('small'),
                'message_new' => Arr::in($user_id, $unreadList)
            ];
        }

        $this->response = json_encode(['status' => 1, 'data' => $response]);
    }

    /**
     * Get new p.m. count for current user
     * @throws JsonException
     */
    public function actionMessagesnewcount()
    {
        // check if authed
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }
        $this->setJsonHeader();

        // get user object
        $user = App::$User->identity();

        // get new message count
        $query = Message::where('target_id', '=', $user->id)
            ->where('readed', '=', 0)->count();

        // set response as json
        $this->response = json_encode(['status' => 1, 'count' => $query]);
    }

    /**
     * List messages with correspondent
     * @param $cor_id
     * @throws JsonException
     */
    public function actionMessageList($cor_id)
    {
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }

        if (!Object::isLikeInt($cor_id) || $cor_id < 1) {
            throw new JsonException('Corresponded id is wrong');
        }

        // get special types for this action
        $queryType = App::$Request->get('type');
        $queryId = (int)App::$Request->get('id');
        // get current user object
        $user = App::$User->identity();

        if (Arr::in($queryType, ['before', 'after']) && (!Object::isLikeInt($queryId) || $queryId < 1)) {
            throw new JsonException('Bad input data');
        }

        $messages = null;
        // sounds like a Hindi code, but we need more closures to organize where conditions
        // after raw: select * from `ffcms_messages` where `id` > ? and ((`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?)) order by `created_at` desc
        // before raw: select * from `ffcms_messages` where (`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?) order by `created_at` desc
        // default raw: select * from `ffcms_messages` where `id` < ? and ((`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?)) order by `created_at` desc
        switch ($queryType) {
            case 'after':
                $messages = Message::where('id', '>', $queryId)
                    ->where(function ($query) use ($cor_id, $user) {
                        $query->where(function ($q) use ($cor_id, $user){
                            $q->where('target_id', '=', $user->getId())
                                ->where('sender_id', '=', $cor_id);
                        })->orWhere(function ($q) use ($cor_id, $user){
                            $q->where('target_id', '=', $cor_id)
                                ->where('sender_id', '=', $user->getId());
                        });
                    });
                break;
            case 'before':
                $messages = Message::where('id', '<', $queryId)
                    ->where(function ($query) use ($cor_id, $user) {
                        $query->where(function ($q) use ($cor_id, $user){
                            $q->where('target_id', '=', $user->getId())
                                ->where('sender_id', '=', $cor_id);
                        })->orWhere(function ($q) use ($cor_id, $user){
                            $q->where('target_id', '=', $cor_id)
                                ->where('sender_id', '=', $user->getId());
                        });
                    });
                break;
            default:
                $messages = Message::where(function($query) use ($cor_id, $user) {
                    $query->where('target_id', '=', $user->getId())
                        ->where('sender_id', '=', $cor_id);
                })->orWhere(function($query) use ($cor_id, $user) {
                    $query->where('target_id', '=', $cor_id)
                        ->where('sender_id', '=', $user->getId());
                });
                break;
        }

        // set response header
        $this->setJsonHeader();

        $messages->orderBy('created_at', 'DESC')
            ->take(self::MSG_TEXT_LIST);

        // check if messages exist
        if ($messages->count() < 1) {
            $this->response = json_encode(['status' => 0, 'text' => 'No messages']);
            return;
        }

        // build response
        $response = null;
        foreach ($messages->get() as $msg) {
            $response[] = [
                'id' => $msg->id,
                'my' => $msg->sender_id === $user->id,
                'message' => App::$Security->strip_tags($msg->message),
                'date' => Date::convertToDatetime($msg->created_at, Date::FORMAT_TO_SECONDS),
                'readed' => $msg->readed
            ];
            // update status to readed
            if ($msg->readed !== 1 && $msg->sender_id !== $user->id) {
                $msg->readed = 1;
                $msg->save();
            }
        }

        $this->response = json_encode(['status' => 1, 'data' => array_reverse($response)]);
    }

    /**
     * Send message via AJAX
     * @param $target_id
     * @throws JsonException
     */
    public function actionMessagesend($target_id)
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new JsonException('Auth required');
        }

        // check input params
        $msg = App::$Security->strip_tags(App::$Request->get('message'));
        if (!Object::isLikeInt($target_id) || $target_id < 1 || String::length($msg) < 1) {
            throw new JsonException('Wrong input data');
        }

        $this->setJsonHeader();
        // get current user object
        $user = App::$User->identity();

        // try to save message
        $message = new Message();
        $message->target_id = $target_id;
        $message->sender_id = $user->id;
        $message->message = $msg;
        $message->save();

        $this->response = json_encode(['status' => 1]);
    }
}