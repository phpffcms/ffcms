<?php

namespace Apps\Controller\Api\Comments;

use Apps\ActiveRecord\CommentPost;
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\App as AppRecord;

/**
 * Trait ActionList
 * @package Apps\Controller\Api\Comments
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionList
{
    /**
     * List comments as json object with defined offset index
     * @param string $index
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function aList(string $index): ?string
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
        $records = $query->with(['user', 'user.profile', 'user.role'])
            ->skip($offset)
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
        $countQuery = CommentPost::where('pathway', '=', $path)->where('moderate', '=', 0);
        // check if comments is depend of language locale
        if ((bool)$configs['onlyLocale'] === true) {
            $countQuery = $countQuery->where('lang', '=', $this->request->getLanguage());
        }
        $count = $countQuery->count();
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
}
