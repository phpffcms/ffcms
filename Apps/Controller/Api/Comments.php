<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\App as AppRecord;
use Apps\Model\Api\Comments\CommentAdd;
use Apps\Model\Api\Comments\CommentAnswerAdd;
use Apps\Model\Api\Comments\CommentPostAdd;
use Apps\Model\Api\Comments\EntityCommentData;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class Comments extends ApiController
{
    const ITEM_PER_PAGE = 10;

    public function actionAdd()
    {
        $this->setJsonHeader();
        $configs = AppRecord::getConfigs('widget', 'Comments');

        $replayTo = (int)App::$Request->request->get('replay-to');
        $model = null;
        // check if its a answer (comment answer type)
        if ($replayTo > 0) {
            $model = new CommentAnswerAdd($configs);
            $model->replayTo = $replayTo;
        } else { // sounds like new comment row
            $model = new CommentPostAdd($configs);
            $model->pathway = App::$Security->strip_tags(App::$Request->request->get('pathway'));
        }

        // pass general comment params to model
        $model->message = App::$Security->secureHtml((string)App::$Request->request->get('message'));
        $model->guestName = App::$Security->strip_tags(App::$Request->request->get('guest-name'));

        // check model conditions before add new row
        if ($model === null || !$model->check()) {
            throw new JsonException('Unknown error');
        }

        // add comment post or answer to database and get response active record row
        $record = $model->buildRecord();
        // pass row to entity builder model
        $response = new EntityCommentData($record);

        return json_encode([
            'status' => 1,
            'data' => $response->make() // build row to standard format
        ]);
    }

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
            throw new JsonException(__('There is no comments found yet. You can be the first!'));
        }

        // build output json data as array
        $data = [];
        foreach ($records as $comment) {
            // prepare specified data to output response, based on entity model
            $commentResponse = new EntityCommentData($comment);

            // build output json data
            $data[] = $commentResponse->make();
            $commentResponse = null;
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
            throw new JsonException(__('No answers for comment is founded'));
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