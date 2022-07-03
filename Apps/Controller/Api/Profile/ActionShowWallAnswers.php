<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\WallAnswer;
use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionShowWallAnswers
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionShowWallAnswers
{
    /**
     * Show all answers for this post id
     * @param string $postId
     * @return string
     * @throws NativeException
     * @return string
     */
    public function showWallAnswers(string $postId): ?string
    {
        $this->setJsonHeader();

        // check input post id num
        if (!Any::isInt($postId) || $postId < 1) {
            throw new NativeException('Wrong input data');
        }

        // try to find this post
        $object = WallPost::find($postId);

        // check if post is exist
        if (!$object) {
            throw new NativeException('Wrong input data');
        }

        // get answer object with relation to user, profile and role table
        $answers = WallAnswer::with(['user', 'user.profile', 'user.role'])
            ->where('post_id', $postId)
            ->orderBy('id', 'DESC')
            ->take(200)
            ->get();

        $response = [];
        $answers->each(function($answer) use (&$response) {
            /** @var WallAnswer $answer */
            if (!$answer->user || $answer->user->id < 1) {
                return;
            }
            // generate response array
            $response[] = [
                'answer_id' => $answer->id,
                'user_id' => $answer->user_id,
                'user_name' => $answer->user->profile->getName(),
                'user_avatar' => $answer->user->profile->getAvatarUrl('small'),
                'user_color' => $answer->user->role->color,
                'answer_message' => $answer->message,
                'answer_date' => Date::humanize($answer->created_at)
            ];
        });

        // encode and show json result
        return json_encode(['status' => 1, 'data' => $response]);
    }
}
