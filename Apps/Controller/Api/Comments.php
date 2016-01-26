<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\App as AppRecord;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class Comments extends ApiController
{
    const ITEM_PER_PAGE = 10;

    public function actionList($index)
    {
        // set header
        $this->setJsonHeader();
        // get config count per page
        $perPage = (int)AppRecord::getConfig('widget', 'Comments', 'perPage');
        // offset can be only integer
        $index = (int)$index;
        $offset = $perPage * $index;
        // get comment target path and check
        $path = (string)App::$Request->query->get('path');
        if (Str::likeEmpty($path)) {
            throw new JsonException('Wrong path');
        }

        // select comments from db and check it
        $records = CommentPost::where('pathway', '=', $path)
            ->skip($offset)
            ->take($perPage)
            ->get();

        if ($records->count() < 1) {
            throw new JsonException('No comments is found');
        }

        // build output json data as array
        $data = [];
        foreach ($records as $comment) {
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

        // calculate comments left count
        $count = CommentPost::where('pathway', '=', $path)->count();
        $count -= $offset + $perPage;
        if ($count < 0) {
            $count = 0;
        }

        return json_encode([
            'status' => 1,
            'data' => $data,
            'leftCount' => $count
        ]);
    }

    public function actionShowanswers($commentId)
    {
        // check input data
        if (!Obj::isLikeInt($commentId) || (int)$commentId < 1) {
            throw new JsonException('Input data is incorrect');
        }

        // get data from db by comment id
        $records = CommentAnswer::where('comment_id', '=', $commentId);
        if ($records->count() < 1) {
            throw new JsonException('No answers for comment is founded');
        }

        // prepare output
        $response = [];
        foreach ($records->get() as $row) {
            $userInstance = $row->getUser();
            $userName = __('Unknown');
            $userAvatar = App::$Alias->scriptUrl . '/upload/user/avatar/small/default.jpg';
            if ($userInstance !== null) {
                if (!Str::likeEmpty($userInstance->getProfile()->nick)) {
                    $userName = $userInstance->getProfile()->nick;
                }
                $userAvatar = $userInstance->getProfile()->getAvatarUrl('small');
            } else {
                if (!Str::likeEmpty($row->guest_name)) {
                    $userName = App::$Security->strip_tags($row->guest_name);
                }
            }

            $response[] = [
                'id' => $row->id,
                'text' => $row->message,
                'date' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_HOUR),
                'user' => [
                    'id' => $row->user_id,
                    'name' => $userName,
                    'avatar' => $userAvatar
                ]
            ];
        }

        return json_encode([
            'status' => 1,
            'data' => $response
        ]);
    }

}