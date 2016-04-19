<?php

namespace Widgets\Front\Newcomment;

use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as AbstractWidget;
use Ffcms\Core\Traits\OopTools;
use Apps\ActiveRecord\CommentPost;

/**
 * New comments widget. Show new comments in system.
 */
class Newcomment extends AbstractWidget
{
    use OopTools;

	public $snippet;
	public $count;
	public $cache;

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
    }

    /**
     * Show latest comments
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::display()
     */
    public function display()
    {
        $classHash = $this->createStringClassSnapshotHash();

        // work with cache and make query
        $records = null;
        if ((int)$this->cache > 0) {
            if (App::$Cache->get('widget.newcomment.' . $classHash) !== null) {
                $records = App::$Cache->get('widget.newcomment.' . $classHash);
            } else {
                $records = $this->makeQuery();
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