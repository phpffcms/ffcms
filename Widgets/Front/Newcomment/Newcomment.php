<?php

namespace Widgets\Front\Newcomment;

use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as AbstractWidget;
use Ffcms\Core\Traits\ClassTools;
use Apps\ActiveRecord\CommentPost;

/**
 * Class Newcomment. New comments widget. Show new comments in system.
 * @package Widgets\Front\Newcomment
 */
class Newcomment extends AbstractWidget
{
    use ClassTools;

    public $snippet;
    public $count;
    public $cache;
    public $lang;

    private $_cacheName;

    /**
     * Set default configs if not passed
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::init()
     */
    public function init(): void
    {
        $cfg = $this->getConfigs();
        if (!$this->snippet) {
            $this->snippet = $cfg['snippet'];
        }

        if (!$this->count) {
            $this->count = (int)$cfg['count'];
        }

        if (!$this->cache) {
            $this->cache = (int)$cfg['cache'];
        }

        if (!$this->lang) {
            $this->lang = App::$Request->getLanguage();
        }


        $this->_cacheName = 'widget.newcomment.' . $this->createStringClassSnapshotHash();
    }

    /**
     * Show latest comments
     * @return string
     * @throws \Ffcms\Core\Exception\JsonException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function display(): ?string
    {
        // work with cache and make query
        $records = null;
        if ((int)$this->cache > 0) {
            // process caching data
            $cache = App::$Cache->getItem($this->_cacheName);
            if (!$cache->isHit()) {
                $cache->set($this->makeQuery());
                $cache->expiresAfter((int)$this->cache);
                App::$Cache->save($cache);
            }

            $records = $cache->get();
        } else {
            $records = $this->makeQuery();
        }

        // check if records is found
        if (!$records) {
            return __('Comments not yet found');
        }

        $commentEntity = null;
        foreach ($records as $record) {
            $commentEntity[] = (new EntityCommentData($record, false))->make();
        }

        // render view
        return App::$View->render('widgets/newcomment/default', [
            'comments' => $commentEntity,
            'snippet' => $this->snippet
        ]);
    }

    /**
     * Make database query and return results
     * @return object
     */
    private function makeQuery()
    {
        $records = CommentPost::with(['user', 'user.profile', 'user.role'])
            ->where('lang', $this->lang)
            ->where('moderate', 0);

        if (!$records || $records->count() < 1) {
            return null;
        }

        return $records->orderBy('id', 'DESC')
        ->take($this->count)
        ->get();
    }
}
