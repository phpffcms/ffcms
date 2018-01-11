<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionFeed
 * @package Apps\Controller\Front\Profile
 * @property \Apps\ActiveRecord\App $application
 * @property Request $request
 * @property View $view
 * @property Response $response
 */
trait ActionFeed
{

    /**
     * Show all users feed activity from wall posts
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function feed(): ?string
    {
        /** @var array $cfg */
        $cfg = $this->application->configs;
        // get pagination page id and calc offset
        $page = (int)$this->request->query->get('page');
        $items = 10;
        if ((int)$cfg['wallPostOnFeed'] >= 1) {
            $items = (int)$cfg['wallPostOnFeed'];
        }

        $offset = $page * $items;

        // total wall posts count
        $query = new WallPost();

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/feed'],
            'page' => $page,
            'step' => $items,
            'total' => $query->count()
        ]);

        // get records from database as object related with User, Role, Profile objects
        $records = $query->with(['senderUser', 'senderUser.role', 'senderUser.profile'])
            ->orderBy('id', 'DESC')
            ->skip($offset)
            ->take($items)
            ->get();

        // render output view
        return $this->view->render('feed', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }
}
