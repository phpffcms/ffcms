<?php
namespace Widgets\Front\Newcontent;

use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as Widget;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Traits\ClassTools;
use Apps\ActiveRecord\Content;

class Newcontent extends Widget
{
    use ClassTools;
    
    public $categories;
    public $cache;
    public $count;
    
    public $tpl = 'widgets/newcontent/default';

    private $_cacheName;
    
    /**
     * Prepare widget. Set default configs if not defined on initialization
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::init()
     */
    public function init()
    {
        $cfg = $this->getConfigs();
        // check if categories is empty
        if (!$this->categories)
            $this->categories = $cfg['categories'];

        // check cache is defined
        if (!$this->cache || !Any::isInt($this->cache))
            $this->cache = (int)$cfg['cache'];

        // check item count is defined
        if (!$this->count|| !Any::isInt($this->count))
            $this->count = (int)$cfg['count'];

        $this->_cacheName = 'widget.newcontent.' . $this->createStringClassSnapshotHash();
    }

    /**
     * Display new content widget logic
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::display()
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function display()
    {
        $query = null;
        // cache is disabled, get result directly
        if ($this->cache === 0) {
            $query = $this->makeQuery();
        } else {
            // try get query result from cache
            $cache = App::$Cache->getItem($this->_cacheName);
            if (!$cache->isHit())
                $cache->set($this->makeQuery())->expiresAfter($this->cache);

            $query = $cache->get();
        }
        
        // check if response is not empty
        if (!$query || $query->count() < 1)
            return __('Content is not founded');
        
        // render view
        return App::$View->render($this->tpl, [
           'records' => $query 
        ]);
    }
    
    /**
     * Make query to database
     * @return object
     */
    private function makeQuery()
    {
        return Content::select(['contents.*', 'content_categories.path as cpath', 'content_categories.title as ctitle'])
            ->whereIn('contents.category_id', $this->categories)
            ->where('contents.display', '=', 1)
            ->join('content_categories', 'content_categories.id', '=', 'contents.category_id', 'left outer')
            ->orderBy('contents.created_at', 'DESC')
            ->take($this->count)->get();
    }
}