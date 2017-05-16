<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\App as AppRecord;
use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Api\Comments\CommentAnswerAdd;
use Apps\Model\Api\Comments\CommentPostAdd;
use Apps\Model\Api\Comments\EntityCommentData;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Comments. View and add comments and answers via json based ajax query's
 * @package Apps\Controller\Api
 */
class Comments extends ApiController
{
    /**
     * Add comment or answer via ajax.
     * @return string
     * @throws NativeException
     */
    public function actionAdd()
    {
        $this->setJsonHeader();
        $configs = AppRecord::getConfigs('widget', 'Comments');

        $replayTo = (int)$this->request->request->get('replay-to');
        $model = null;
        // check if its a answer (comment answer type)
        if ($replayTo > 0) {
            $model = new CommentAnswerAdd($configs);
            $model->replayTo = $replayTo;
        } else { // sounds like new comment row
            $model = new CommentPostAdd($configs);
            $model->pathway = App::$Security->strip_tags($this->request->request->get('pathway'));
        }

        // pass general comment params to model
        $model->message = App::$Security->secureHtml((string)$this->request->request->get('message'));
        $model->guestName = App::$Security->strip_tags($this->request->request->get('guest-name'));

        // check model conditions before add new row
        if ($model === null || !$model->check()) {
            throw new NativeException('Unknown error');
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

    /**
     * List comments as json object with defined offset index
     * @param int $index
     * @return string
     * @throws NotFoundException
     */
    public function actionList($index)
    {
        // set header
        $this->setJsonHeader();
        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');
        // items per page
        $perPage = (int)$configs['perPage'];
        // offset can be only integer
        $index = (int)$index;
        $offset = $perPage * $index;
        // get comment target path and check
        $path = (string)$this->request->query->get('path');
        if (Str::likeEmpty($path)) {
            throw new NotFoundException('Wrong path');
        }

        // select comments from db and check it
        $query = CommentPost::where('pathway', '=', $path)
            ->where('moderate', '=', 0);

        // check if comments is depend of language locale
        if ((bool)$configs['onlyLocale'] === true) {
            $query = $query->where('lang', '=', $this->request->getLanguage());
        }

        // get comments with offset and limit
        $records = $query->skip($offset)
            ->take($perPage)
            ->get();

        // check if records is not empty
        if ($records->count() < 1) {
            throw new NotFoundException(__('There is no comments found yet. You can be the first!'));
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
        $count = CommentPost::where('pathway', '=', $path)->where('moderate', '=', 0)->count();
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

    /**
     * List answers by comment id as json object
     * @param int $commentId
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function actionShowanswers($commentId)
    {
        $this->setJsonHeader();
        // check input data
        if (!Obj::isLikeInt($commentId) || (int)$commentId < 1) {
            throw new ForbiddenException('Input data is incorrect');
        }

        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');

        // get data from db by comment id
        $records = CommentAnswer::where('comment_id', '=', $commentId)
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

    /**
     * Get commentaries count for pathway. Pathway should be array [itemId => pathway]
     * @throws NativeException
     * @return string
     */
    public function actionCount()
    {
        // set headers
        $this->setJsonHeader();
        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');
        // get path array from request
        $path = $this->request->query->get('path');
        if (!Obj::isArray($path) || count($path) < 1) {
            throw new NativeException('Wrong query params');
        }

        $count = [];
        // for each item in path array calculate comments count
        foreach ($path as $id => $uri) {
            $query = CommentPost::where('pathway', '=', $uri)->where('moderate', '=', 0);
            // check if comments is depend of language locale
            if ((int)$configs['onlyLocale'] === 1) {
                $query = $query->where('lang', '=', $this->request->getLanguage());
            }
            // set itemId => count
            $count[(int)$id] = $query->count();
        }
        // render json response
        return json_encode(['status' => 1, 'count' => $count]);
    }
}