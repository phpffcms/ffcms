<?php

namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\WallAnswer;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionDeleteAnswerOwner
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader
 */
trait ActionDeleteAnswerOwner
{
    /**
     * Delete answer by answer owner or wall owner
     * @param $answerId
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws NotFoundException
     * @throws \Exception
     */
    public function deleteAnswerOwner(string $answerId): ?string
    {
        $this->setJsonHeader();
        // hello script kiddy, you must be auth ;)
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        // answer id must be an unsigned integer
        if (!Any::isInt($answerId) || $answerId < 1) {
            throw new NativeException('Wrong input data');
        }

        /** @var WallAnswer $findAnswer */
        $findAnswer = WallAnswer::find($answerId);
        // check if this answer id exist
        if (!$findAnswer) {
            throw new NotFoundException('Wrong input data');
        }

        // get current viewer
        $viewer = App::$User->identity();
        // get post info
        $postInfo = $findAnswer->post;

        // if not a target user of answer and not answer owner - lets throw exception
        if ($postInfo->target_id !== $viewer->id && $findAnswer->user_id !== $viewer->id) {
            throw new ForbiddenException('Access declined!');
        }

        // all is ok, lets remove this answer ;)
        $findAnswer->delete();

        return json_encode([
            'status' => 1,
            'message' => 'ok'
        ]);
    }
}
