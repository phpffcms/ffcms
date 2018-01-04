<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Message;
use Apps\ActiveRecord\ProfileRating;
use Apps\ActiveRecord\UserNotification;
use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Apps\Model\Front\Profile\EntityAddNotification;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\MySqlConnection;

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

    /**
     * Get wall answer's count by post-ids list
     * @param int $postIds
     * @throws NativeException
     * @return string
     */
    public function actionWallanswercount($postIds)
    {
        // set header
        $this->setJsonHeader();
        // check query length
        if (Any::isEmpty($postIds))
            throw new NativeException('Wrong input count');

        $list = explode(',', $postIds);
        $itemCount = count($list);
        // empty or is biggest then limit?
        if ($itemCount < 1 || $itemCount > self::ITEM_PER_PAGE)
            throw new NativeException('Wrong input count');

        // prepare response
        $response = [];
        foreach ($list as $post) {
            $response[$post] = WallAnswer::where('post_id', '=', $post)->count();
        }

        // display json data
        return json_encode([
            'status' => 1,
            'data' => $response
        ]);
    }

    /**
     * Show all answers for this post id
     * @param int $postId
     * @return string
     * @throws NativeException
     * @return string
     */
    public function actionShowwallanswers($postId)
    {
        // check input post id num
        if (!Any::isInt($postId) || $postId < 1)
            throw new NativeException('Wrong input data');

        // try to find this post
        $object = WallPost::find($postId);

        // check if post is exist
        if (!$object)
            throw new NativeException('Wrong input data');

        // get answer object with relation to user, profile and role table
        $answers = WallAnswer::with(['user', 'user.profile', 'user.role'])
            ->where('post_id', $postId)
            ->orderBy('id', 'DESC')
            ->take(200)
            ->get();

        $response = [];
        /** @var WallAnswer[] $answers */
        foreach ($answers as $answer) {
            // check if user exist
            if ($answer->user === null || $answer->user->id < 1) {
                continue;
            }
            // generate response array
            $response[] = [
                'answer_id' => $answer->id,
                'user_id' => $answer->user_id,
                'user_nick' => $answer->user->profile->getNickname(),
                'user_avatar' => $answer->user->profile->getAvatarUrl('small'),
                'user_color' => $answer->user->role->color,
                'answer_message' => $answer->message,
                'answer_date' => Date::humanize($answer->created_at)
            ];
        }

        // encode and show json result
        return json_encode(['status' => 1, 'data' => $response]);
    }

    /**
     * Add new post answer from AJAX post
     * @param int $postId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSendwallanswer($postId)
    {
        // not auth? what are you doing there? ;)
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        // no post id? wtf you doing man!
        if (!Any::isInt($postId) || $postId < 1)
            throw new NativeException('Wrong input data');

        // get current(sender) user object
        $viewer = App::$User->identity();

        // get message from post and validate minlength
        $message = $this->request->get('message');
        $message = App::$Security->strip_tags($message);
        if (!Any::isStr($message) || Str::length($message) < 3)
            throw new ForbiddenException('Wrong input data');

        // try to find this post
        $wallPost = WallPost::where('id', '=', $postId);
        if ($wallPost->count() < 1)
            throw new NativeException('Wrong input data');

        $wallRow = $wallPost->first();
        $target_id = $wallRow->target_id;
        // check if in blacklist
        if (!Blacklist::check($viewer->id, $target_id))
            throw new ForbiddenException('User is blocked!');

        // check delay between user last post and current
        $lastAnswer = WallAnswer::where('user_id', '=', App::$User->identity()->getId())
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!$lastAnswer) {
            $now = time();
            $answerTime = Date::convertToTimestamp($lastAnswer->created_at);
            $cfgs = \Apps\ActiveRecord\App::getConfigs('app', 'Profile');
            // hmm, maybe past less then delay required?
            if ($now - (int)$cfgs['delayBetweenPost'] < $answerTime)
                throw new ForbiddenException('Delay between answers not pass');
        }

        // make new row ;)
        $answers = new WallAnswer();
        $answers->post_id = $postId;
        $answers->user_id = $viewer->id;
        $answers->message = $message;
        $answers->save();

        // add notification for target user
        if ($viewer->id !== $target_id) {
            $notify = new EntityAddNotification($target_id);
            $notify->add('/profile/show/' . $target_id . '#wall-post-' . $wallRow->id, EntityAddNotification::MSG_ADD_WALLANSWER, [
                'snippet' => Text::snippet($message, 50),
                'post' => $wallRow->message
            ]);
        }

        // send "ok" response
        $this->setJsonHeader();
        return json_encode(['status' => 1, 'message' => 'ok']);
    }

    /**
     * Delete answer by answer owner or wall owner
     * @param $answerId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws NotFoundException
     * @throws \Exception
     */
    public function actionDeleteanswerowner($answerId)
    {
        $this->setJsonHeader();
        // hello script kiddy, you must be auth ;)
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        // answer id must be an unsigned integer
        if (!Any::isInt($answerId) || $answerId < 1)
            throw new NativeException('Wrong input data');

        /** @var WallAnswer $findAnswer */
        $findAnswer = WallAnswer::find($answerId);
        // check if this answer id exist
        if (!$findAnswer)
            throw new NotFoundException('Wrong input data');

        // get current viewer
        $viewer = App::$User->identity();
        // get post info
        $postInfo = $findAnswer->post;

        // if not a target user of answer and not answer owner - lets throw exception
        if($postInfo->target_id !== $viewer->id && $findAnswer->user_id !== $viewer->id)
            throw new ForbiddenException('Access declined!');

        // all is ok, lets remove this answer ;)
        $findAnswer->delete();

        return json_encode([
           'status' => 1,
            'message' => 'ok'
        ]);
    }

    /**
     * Load user dialog list based on offset
     * @param int $offset
     * @param int $new
     * @return string
     * @throws ForbiddenException
     */
    public function actionListmessagedialog($offset = 0, $new = 0)
    {
        // check is user auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }
        $this->setJsonHeader();

        // check is offset is int
        if ($offset !== 0 && !Any::isInt($offset))
            $offset = 0;
        ++$offset;

        // get user person
        $user = App::$User->identity();

        $records = Message::select('readed', 'target_id', 'sender_id', Capsule::raw('max(created_at) as cmax'))
            ->where('target_id', '=', $user->id)
            ->orWhere('sender_id', '=', $user->id)
            ->orderBy('readed', 'ASC') //- error happens, cuz readed is boolean in pgsql
            ->orderBy('cmax', 'DESC')
            ->groupBy(['sender_id', 'target_id', 'readed']) // multiple order's can throw exception on some kind of database engines
            ->take($offset * self::MSG_USER_LIST)
            ->get();

        $userList = [];
        $unreadList = [];

        if (Any::isInt($new) && $new > 0 && App::$User->isExist($new))
            $userList[] = $new;

        // there is 2 way of messages: me->user; user->me, try to parse it
        foreach ($records as $row) {
            // target is not myself? then i'm - sender (remote user is target: my->to_user)
            if ($row->target_id !== $user->id)
                $userList[] = $row->target_id;

            // sender is not myself? then i'm - target (remote user is sender user->to_me)
            if ($row->sender_id !== $user->id) {
                $userList[] = $row->sender_id;
                if ((bool)$row->readed !== true)
                    $unreadList[] = $row->sender_id;
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
                'user_nick' => $identity->profile->getNickname(),
                'user_avatar' => $identity->profile->getAvatarUrl('small'),
                'message_new' => Arr::in($user_id, $unreadList),
                'user_block' => !Blacklist::check($user->id, $identity->id)
            ];
        }

        return json_encode(['status' => 1, 'data' => $response]);
    }

    /**
     * Get user p.m and notifications count
     * @return string
     * @throws ForbiddenException
     */
    public function actionNotifications()
    {
        // check if authed
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        $this->setJsonHeader();

        // get user object
        $user = App::$User->identity();

        // get messages count
        $messagesCount = Message::where('target_id', '=', $user->id)
            ->where('readed', '=', 0)->count();

        // get notifications count
        $notificationsCount = UserNotification::where('user_id', '=', $user->id)
            ->where('readed', '=', 0)->count();

        return json_encode([
            'status' => 1,
            'notify' => $notificationsCount,
            'messages' => $messagesCount,
            'summary' => $notificationsCount + $messagesCount
        ]);
    }

    /**
     * List messages with correspondent
     * @param $cor_id
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws NativeException
     */
    public function actionMessageList($cor_id)
    {
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        if (!Any::isInt($cor_id) || $cor_id < 1)
            throw new NotFoundException('Corresponded id is wrong');

        // get special types for this action
        $queryType = $this->request->get('type');
        $queryId = (int)$this->request->get('id');
        // get current user object
        $user = App::$User->identity();

        if (Arr::in($queryType, ['before', 'after']) && (!Any::isInt($queryId) || $queryId < 1))
            throw new NativeException('Bad input data');

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
        if ($messages->count() < 1)
            return json_encode(['status' => 0, 'text' => 'No messages']);

        // build response
        $response = null;
        foreach ($messages->get() as $msg) {
            /** @var Message $msg */
            $response[] = [
                'id' => $msg->id,
                'my' => $msg->sender_id === $user->id,
                'message' => $msg->message,
                'date' => Date::convertToDatetime($msg->created_at, Date::FORMAT_TO_SECONDS),
                'readed' => $msg->readed
            ];

            // update status to readed
            if ((bool)$msg->readed !== true && $msg->sender_id !== $user->id) {
                $msg->readed = true;
                $msg->save();
            }
        }

        return json_encode([
            'status' => 1,
            'data' => array_reverse($response),
            'blocked' => !Blacklist::check($user->id, $cor_id)
        ]);
    }

    /**
     * Send message via AJAX
     * @param $targetId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     */
    public function actionMessagesend($targetId)
    {
        // check if user is auth
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        // get current user object and check in blacklist
        $user = App::$User->identity();
        if (!Blacklist::check($user->id, $targetId))
            throw new ForbiddenException('In blacklist');

        // check input params
        $msg = App::$Security->strip_tags($this->request->get('message'));
        if (!Any::isInt($targetId) || $targetId < 1 || Str::length($msg) < 1)
            throw new NativeException('Wrong input data');

        $this->setJsonHeader();

        // try to save message
        $message = new Message();
        $message->target_id = $targetId;
        $message->sender_id = $user->id;
        $message->message = $msg;
        $message->save();

        return json_encode(['status' => 1]);
    }

    /**
     * Change user rating action
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionChangerating()
    {
        if (!App::$User->isAuth())
            throw new ForbiddenException('Auth required');

        $this->setJsonHeader();

        // get operation type and target user id
        $targetId = (int)$this->request->get('target');
        $type = $this->request->get('type');

        // check type of query
        if ($type !== '+' && $type !== '-')
            throw new NativeException('Wrong data');

        // check if passed user id is exist
        if (!Any::isInt($targetId) || $targetId < 1 || !App::$User->isExist($targetId))
            throw new NotFoundException('Wrong user info');

        $cfg = \Apps\ActiveRecord\App::getConfigs('app', 'Profile');
        // check if rating is enabled for website
        if (!(bool)$cfg['rating'])
            throw new NativeException('Rating is disabled');

        // get target and sender objects
        $target = App::$User->identity($targetId);
        $sender = App::$User->identity();

        // disable self-based changes ;)
        if ($target->getId() === $sender->getId())
            throw new ForbiddenException('Self change prevented');

        // check delay
        $diff = Date::convertToDatetime(time() - $cfg['ratingDelay'], Date::FORMAT_SQL_TIMESTAMP);

        $query = ProfileRating::where('target_id', $target->getId())
            ->where('sender_id', $sender->getId())
            ->where('created_at', '>=', $diff)
            ->orderBy('id', 'DESC');
        if ($query->count() > 0)
            throw new ForbiddenException('Delay required');

        // delay is ok, lets insert a row
        $record = new ProfileRating();
        $record->target_id = $target->getId();
        $record->sender_id = $sender->getId();
        $record->type = $type;
        $record->save();

        if ($type === '+') {
            $target->profile->rating += 1;
        } else {
            $target->profile->rating -= 1;
        }
        $target->profile->save();

        return json_encode(['status' => 1, 'data' => 'ok']);
    }
}