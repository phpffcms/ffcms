<?php

namespace Widgets\Front\Contenttag;

use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as AbstractWidget;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Traits\ClassTools;
use Apps\ActiveRecord\ContentTag as TagRecord;

/**
 * Class Contenttag. Display most popular tags in block
 * @package Widgets\Front\Contenttag
 */
class Contenttag extends AbstractWidget
{
    use ClassTools;

    public $count;
    public $cache;

    public $tpl = 'widgets/contenttag/default';

    private $_lang;
    private $_cacheName;

    /**
     * Set default configurations if not defined
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::init()
     */
    public function init(): void
    {
        $cfg = $this->getConfigs();
        // check cache is defined
        if (!$this->cache|| !Any::isInt($this->cache)) {
            $this->cache = $cfg['cache'];
        }

        // check tag count is defined
        if (!$this->count || !Any::isInt($this->count)) {
            $this->count = $cfg['count'];
        }

        $this->_lang = App::$Request->getLanguage();
        $this->_cacheName = 'widget.contenttag.' . $this->createStringClassSnapshotHash();
    }

    /**
     * Display widget info
     * @return string|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function display(): ?string
    {
        // get records rows from cache or directly from db
        $records = null;
        if ($this->cache === 0) {
            $records = $this->makeQuery();
        } else {
            $cache = App::$Cache->getItem($this->_cacheName);
            if (!$cache->isHit()) {
                $cache->set($this->makeQuery())->expiresAfter($this->cache);
                App::$Cache->save($cache);
            }
            $records = $cache->get();
        }

        // check if result is not empty
        if (!$records || $records->count() < 1) {
            return __('Content tags is not found');
        }

        // render view
        return App::$View->render($this->tpl, [
            'records' => $records
        ]);
    }

    /**
     * Make query to database via active record
     * @return object
     */
    private function makeQuery()
    {
        return TagRecord::select([
            'tag',
            App::$Database->getConnection()->raw('COUNT(tag) AS count')
        ])->where('lang', '=', $this->_lang)
            ->groupBy('tag')
            ->orderBy('count', 'DESC')
            ->take($this->count)
            ->get();
    }
}
