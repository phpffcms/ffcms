<?php

namespace Widgets\Front\Contenttag;

use Ffcms\Core\App;
use Extend\Core\Arch\FrontWidget as AbstractWidget;
use Ffcms\Core\Traits\ClassTools;
use Ffcms\Core\Helper\Type\Obj;
use Apps\ActiveRecord\ContentTag as TagRecord;

class Contenttag extends AbstractWidget
{
	use ClassTools;

	public $count;
	public $cache;

	public $tpl = 'widgets/contenttag/default';

	/**
	 * Set default configurations if not defined
	 * {@inheritDoc}
	 * @see \Ffcms\Core\Arch\Widget::init()
	 */
    public function init()
    {
        $cfg = $this->getConfigs();
        // check cache is defined
        if ($this->cache === null || !Obj::isLikeInt($this->cache)) {
            $this->cache = (int)$cfg['cache'];
        }
        // check tag count is defined
        if ($this->count === null || !Obj::isLikeInt($this->count)) {
            $this->count = (int)$cfg['count'];
        }
    }

    /**
     * Display widget info
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Widget::display()
     */
    public function display()
    {
        // get special properties hash
    	$classHash = $this->createStringClassSnapshotHash();

    	// get records rows from cache or directly from db
    	$records = null;
    	if ($this->cache === 0) {
    	    $records = $this->makeQuery();
    	} else {
    	    $records = App::$Cache->get('widget.contenttag.' . $classHash);
    	    if ($records === null) {
    	        $records = $this->makeQuery();
    	        App::$Cache->set('widget.contenttag' . $classHash, $records, $this->cache);
    	    }
    	}

    	// check if result is not empty
        if ($records === null || $records->count() < 1) {
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
    	    App::$Database->getConnection()->raw('SQL_CALC_FOUND_ROWS tag'),
    	    App::$Database->getConnection()->raw('COUNT(*) AS count')
    	])->groupBy('tag')
        	->orderBy('count', 'DESC')
        	->take($this->count)
        	->get();
    }
}