<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Message;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Class ActionMessageList
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader
 */
trait ActionMessageList
{
    /**
     * List messages with correspondent
     * @param $corId
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws NativeException
     */
    public function messageList($corId): ?string
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        if (!Any::isInt($corId) || $corId < 1) {
            throw new NotFoundException('Corresponded id is wrong');
        }

        // get special types for this action
        $queryType = $this->request->get('type');
        $queryId = (int)$this->request->get('id');
        // get current user object
        $user = App::$User->identity();

        if (Arr::in($queryType, ['before', 'after']) && (!Any::isInt($queryId) || $queryId < 1)) {
            throw new NativeException('Bad input data');
        }

        $messages = null;
        // sounds like a Hindi code, but we need more closures to organize where conditions
        // after raw: select * from `ffcms_messages` where `id` > ? and ((`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?)) order by `created_at` desc
        // before raw: select * from `ffcms_messages` where (`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?) order by `created_at` desc
        // default raw: select * from `ffcms_messages` where `id` < ? and ((`target_id` = ? and `sender_id` = ?) or (`target_id` = ? and `sender_id` = ?)) order by `created_at` desc
        switch ($queryType) {
            case 'after':
                $messages = Message::where('id', '>', $queryId)
                    ->where(function ($query) use ($corId, $user) {
                        $query->where(function ($q) use ($corId, $user) {
                            $q->where('target_id', '=', $user->getId())
                                ->where('sender_id', '=', $corId);
                        })->orWhere(function ($q) use ($corId, $user) {
                            $q->where('target_id', '=', $corId)
                                ->where('sender_id', '=', $user->getId());
                        });
                    });
                break;
            case 'before':
                $messages = Message::where('id', '<', $queryId)
                    ->where(function ($query) use ($corId, $user) {
                        $query->where(function ($q) use ($corId, $user) {
                            $q->where('target_id', '=', $user->getId())
                                ->where('sender_id', '=', $corId);
                        })->orWhere(function ($q) use ($corId, $user) {
                            $q->where('target_id', '=', $corId)
                                ->where('sender_id', '=', $user->getId());
                        });
                    });
                break;
            default:
                $messages = Message::where(function ($query) use ($corId, $user) {
                    $query->where('target_id', '=', $user->getId())
                        ->where('sender_id', '=', $corId);
                })->orWhere(function ($query) use ($corId, $user) {
                    $query->where('target_id', '=', $corId)
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
            return json_encode(['status' => 0, 'text' => 'No messages']);
        }

        // build response
        $response = [];
        $messages->get()->each(function ($msg) use (&$response, $user){
            /** @var Message $msg */
            $response[] = [
                'id' => $msg->id,
                'my' => $msg->sender_id === $user->id,
                'message' => $msg->message,
                'date' => Date::convertToDatetime($msg->created_at, Date::FORMAT_TO_SECONDS),
                'readed' => $msg->readed
            ];

            // update status to readed
            if (!(bool)$msg->readed && $msg->sender_id !== $user->id) {
                $msg->readed = true;
                $msg->save();
            }
        });

        return json_encode([
            'status' => 1,
            'data' => array_reverse($response),
            'blocked' => !Blacklist::check($user->id, $corId)
        ]);
    }
}
