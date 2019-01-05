<?php

namespace Apps\Controller\Api\Comments;

use Apps\ActiveRecord\App as AppRecord;
use Apps\ActiveRecord\CommentPost;
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

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
     * @param string $appName
     * @param string $appId
     * @return string
     * @throws NotFoundException
     */
    public function aList(string $appName, string $appId): ?string
    {
        // set header
        $this->setJsonHeader();
        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');
        // items per page
        $perPage = (int)$configs['perPage'];
        // offset can be only integer
        $index = (int)$this->request->query->get('offset', 0);
        $offset = $perPage * $index;

        // select comments from db and check it
        $query = CommentPost::where('app_name', $appName)
            ->where('app_relation_id', $appId)
            ->where('moderate', false);

        // check if comments is depend of language locale
        if ((bool)$configs['onlyLocale']) {
            $query = $query->where('lang', $this->request->getLanguage());
        }

        // calculate total comment count
        $count = $query->count();

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
        $records->each(function ($comment) use (&$data){
            $data[] = (new EntityCommentData($comment))->make();
        });

        // reduce count to current offset
        $count -= $offset + $perPage;

        // render output json
        return json_encode([
            'status' => 1,
            'data' => $data,
            'leftCount' => $count < 0 ? 0 : $count
        ]);
    }
}
