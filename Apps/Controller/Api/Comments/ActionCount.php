<?php

namespace Apps\Controller\Api\Comments;

use Apps\ActiveRecord\App as AppRecord;
use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionCount
 * @package Apps\Controller\Api\Comments
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionCount
{
    /**
     * Get comment count by $appName and array of ids
     * @param $appName
     * @return string
     * @throws NativeException
     */
    public function cnt(string $appName): ?string
    {
        // set headers
        $this->setJsonHeader();
        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');
        // get path array from request
        $ids = $this->request->query->get('id');
        if (!Any::isArray($ids) || count($ids) < 1) {
            throw new NativeException('Wrong query params');
        }

        $count = [];
        // for each item in path array calculate comments count
        foreach ($ids as $id) {
            $query = CommentPost::where('app_name', $appName)
                ->where('app_relation_id', (int)$id)
                ->where('moderate', '=', 0);
            // check if comments is depend of language locale
            if ((bool)$configs['onlyLocale']) {
                $query = $query->where('lang', '=', $this->request->getLanguage());
            }
            // set itemId => count
            $count[(int)$id] = $query->count();
        }
        // render json response
        return json_encode(['status' => 1, 'count' => $count]);
    }
}
