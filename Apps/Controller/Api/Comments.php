<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\CommentPost;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class Comments extends ApiController
{
    const ITEM_PER_PAGE = 10;

    public function actionList($offset)
    {
        // set header
        $this->setJsonHeader();
        // offset can be only integer
        $offset = (int)$offset;
        // get comment target path and check
        $path = (string)App::$Request->query->get('path');
        if (Str::likeEmpty($path)) {
            throw new JsonException('Wrong path');
        }

        // select comments from db and check it
        $records = CommentPost::where('pathway', '=', $path)
            ->skip($offset * self::ITEM_PER_PAGE)
            ->take(self::ITEM_PER_PAGE);

        if ($records->count() < 1) {
            throw new JsonException('No comments is found');
        }

        // build output json data as array
        $data = [];
        foreach ($records->get() as $comment) {
            // comment can be passed from registered user (with unique ID) or from guest (without ID)
            $userName = __('Unknown');
            $userAvatar = App::$Alias->scriptUrl . '/upload/user/avatar/small/default.jpg';
            $userObject = $comment->getUser();
            if ($userObject !== null) {
                $userName = $userObject->getProfile()->nick;
                $userAvatar = $userObject->getProfile()->getAvatarUrl('small');
            } else {
                if (!Str::likeEmpty($comment->guest_name)) {
                    $userName = App::$Security->strip_tags($comment->guest_name);
                }
            }

            // build output json data
            $data[] = [
                'id' => $comment->id,
                'text' => $comment->message,
                'date' => Date::convertToDatetime($comment->created_at, Date::FORMAT_TO_HOUR),
                'user' => [
                    'id' => $comment->user_id,
                    'name' => $userName,
                    'avatar' => $userAvatar
                ],
                'answers' => $comment->getAnswerCount()
            ];
        }

        $this->response = json_encode([
            'status' => 1,
            'data' => $data
        ]);
    }

}