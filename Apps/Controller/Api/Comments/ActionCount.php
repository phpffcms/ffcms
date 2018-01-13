<?php

namespace Apps\Controller\Api\Comments;

use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\App as AppRecord;

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
     * Get commentaries count for pathway. Pathway should be array [itemId => pathway]
     * @return string
     * @throws NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function cnt(): ?string
    {
        // set headers
        $this->setJsonHeader();
        // get configs
        $configs = AppRecord::getConfigs('widget', 'Comments');
        // get path array from request
        $path = $this->request->query->get('path');
        if (!Any::isArray($path) || count($path) < 1) {
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
