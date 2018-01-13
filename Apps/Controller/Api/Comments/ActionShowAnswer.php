<?php

namespace Apps\Controller\Api\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\App as AppRecord;

/**
 * Trait ActionShowAnswer
 * @package Apps\Controller\Api\Comments
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionShowAnswer
{
    /**
     * List answers by comment id as json object
     * @param string $commentId
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function showAnswers(string $commentId): ?string
    {
        $this->setJsonHeader();
        // check input data
        if (!Any::isInt($commentId) || $commentId < 1) {
            throw new ForbiddenException('Input data is incorrect');
        }

        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');

        // get data from db by comment id
        $records = CommentAnswer::with(['user', 'user.profile', 'user.role'])
            ->where('comment_id', '=', $commentId)
            ->where('moderate', '=', 0);
        if ((int)$configs['onlyLocale'] === 1) {
            $records = $records->where('lang', '=', $this->request->getLanguage());
        }

        // check objects count
        if ($records->count() < 1) {
            throw new NotFoundException(__('No answers for comment is founded'));
        }

        // prepare output
        $response = [];
        foreach ($records->get() as $row) {
            $commentAnswer = new EntityCommentData($row);
            $response[] = $commentAnswer->make();
        }

        return json_encode([
            'status' => 1,
            'data' => $response
        ]);
    }
}
