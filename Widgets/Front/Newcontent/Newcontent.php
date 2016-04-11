<?php
namespace Widgets\Front\Newcontent;

use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as Widget;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Traits\OopTools;
use Apps\ActiveRecord\Content;

class Newcontent extends Widget
{
    use OopTools;
    
    public $categories;
    public $cache;
    public $count;
    
    public $tpl = 'widgets/newcontent/default';
    
    /**
     * Prepare widget. Set default configs if not defined on initialization
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::init()
     */
    public function init()
    {
        $cfg = $this->getConfigs();
        // check if categories is empty
        if ($this->categories === null) {
            $this->categories = Serialize::decode($cfg['categories']);
        }
        // check cache is defined
        if ($this->cache === null || !Obj::isLikeInt($this->cache)) {
            $this->cache = (int)$cfg['cache'];
        }
        // check item count is defined
        if ($this->count === null || !Obj::isLikeInt($this->count)) {
            $this->count = (int)$cfg['count'];
        }
    }
    
    /**
     * Display new content widget logic
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::display()
     */
    public function display()
    {
        // get unique instance hash with current params
        $cacheHash = $this->createStringClassSnapshotHash();
        
        $query = null;
        // cache is disabled, get result directly
        if ($this->cache === 0) {
            $query = $this->makeQuery();
        } else {
            // try get query result from cache
            $query = App::$Cache->get('widget.newcontent.' . $cacheHash);
            if ($query === null) {
                // if query is not cached make it
                $query = $this->makeQuery();
                // and save result to cache
                App::$Cache->set('widget.newcontent.' . $cacheHash, $query, $this->cache);
            }
        }
        
        // check if response is not empty
        if ($query->count() < 1) {
            return __('Content is not founded');
        }
        
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
                ->join('content_categories', 'content_categories.id', '=', 'contents.category_id', 'left outer')
                ->orderBy('contents.created_at', 'DESC')
                ->take($this->count)->get();
    }
}