<?php

namespace Widgets\Front\Newcomment;

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

    private $_cacheName;

	/**
	 * Set default configs if not passed
	 * {@inheritDoc}
	 * @see \Ffcms\Core\Arch\Widget::init()
	 */
    public function init()
    {
    	$cfg = $this->getConfigs();
    	if ($this->snippet === null) {
    	    $this->snippet = $cfg['snippet'];
    	}
    	if ($this->count === null) {
    	    $this->count = $cfg['count'];
    	}
    	if ($this->cache === null) {
    	    $this->cache = $cfg['cache'];
    	}

        $this->_cacheName = 'widget.newcomment.' . $this->createStringClassSnapshotHash();
    }

    /**
     * Show latest comments
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::display()
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function display()
    {
        // work with cache and make query
        $records = null;
        if ((int)$this->cache > 0) {
            if (App::$Cache->get($this->_cacheName) !== null) {
                $records = App::$Cache->get($this->_cacheName);
            } else {
                $records = $this->makeQuery();
                App::$Cache->set($this->_cacheName, $records, $this->cache);
            }
        } else {
            $records = $this->makeQuery();
        }

        // check if records is found
        if ($records === null) {
            return __('Comments not yet found');
        }

        // render view
    	return App::$View->render('widgets/newcomment/default', [
    	    'records' => $records,
    	    'snippet' => $this->snippet
    	]);
    }

    /**
     * Make database query and return results
     * @return object
     */
    private function makeQuery()
    {
        $records = CommentPost::where('lang', '=', App::$Request->getLanguage())
            ->where('moderate', '=', 0);

        if ($records === null || $records->count() < 1) {
            return null;
        }

        return $records->orderBy('id', 'DESC')
        ->take($this->count)
        ->get();
    }
}