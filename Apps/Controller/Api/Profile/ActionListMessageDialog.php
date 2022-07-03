<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Message;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Trait ActionListMessageDialog
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader
 */
trait ActionListMessageDialog
{
    /**
     * Load user dialog list based on offset
     * @param int $offset
     * @param int $new
     * @return string
     * @throws ForbiddenException
     */
    public function listMessageDialog($offset = 0, $new = 0): ?string
    {
        // check is user auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }
        $this->setJsonHeader();

        // check is offset is int
        if ($offset !== 0 && !Any::isInt($offset)) {
            $offset = 0;
        }
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

        if (Any::isInt($new) && $new > 0 && App::$User->isExist($new)) {
            $userList[] = $new;
        }

        $records->each(function($row) use (&$userList, $user){
            // target is not myself? then i'm - sender (remote user is target: my->to_user)
            if ($row->target_id !== $user->id) {
                $userList[] = $row->target_id;
            }

            // sender is not myself? then i'm - target (remote user is sender user->to_me)
            if ($row->sender_id !== $user->id) {
                $userList[] = $row->sender_id;
                if ((bool)$row->readed !== true) {
                    $unreadList[] = $row->sender_id;
                }
            }
        });

        // store only unique users in dialog
        $userList = array_unique($userList, SORT_NUMERIC);
        // generate json response based on userList and unreadList
        $response = [];
        foreach ($userList as $user_id) {
            $identity = App::$User->identity($user_id);
            if (!$identity) {
                continue;
            }

            $response[] = [
                'user_id' => $user_id,
                'user_name' => $identity->profile->getName(),
                'user_avatar' => $identity->profile->getAvatarUrl('small'),
                'message_new' => Arr::in($user_id, $unreadList),
                'user_block' => !Blacklist::check($user->id, $identity->id)
            ];
        }

        return json_encode(['status' => 1, 'data' => $response]);
    }
}
