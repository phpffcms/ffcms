<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content as ContentEntity;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class EntityContentSearch. Search similar content items.
 * @package Apps\Model\Front\Content
 */
class EntityContentSearch extends Model
{
    const MAX_ITEMS = 5;
    const MIN_QUERY_LENGTH = 2;
    const SEARCH_BY_WORDS_COUNT = 3;
    const CACHE_TIME = 120; // seconds

    public $items;

    private $_terms;
    private $_skip = [0];
    private $_categoryId;
    private $_records;

    /**
     * EntityContentSearch constructor. Pass search terms (query string) to model and used items to skip it by id.
     * @param $terms
     * @param int|array $skipIds
     * @param int|null $categoryId
     */
    public function __construct($terms, $skipIds = 0, $categoryId = null)
    {
        $this->_terms = App::$Security->strip_tags(trim($terms, ' '));
        $this->_categoryId = $categoryId;
        if (Obj::isLikeInt($skipIds)) {
            $this->_skip = [$skipIds];
        } elseif (Obj::isArray($skipIds)) {
            $this->_skip = $skipIds;
        }
        parent::__construct();
    }

    /**
     * Prepare conditions to build content list
     * @throws NotFoundException
     */
    public function before()
    {
        // check length of passed terms
        if (!Obj::isString($this->_terms) || Str::length($this->_terms) < self::MIN_QUERY_LENGTH) {
            throw new NotFoundException(__('Search terms is too short'));
        }

        // try to get this slow query from cache
        $records = App::$Cache->get('entity.content.search.index.' . $this->_skip);
        if ($records === null) {
            $records = ContentEntity::whereNotIn('id', $this->_skip)
                ->where('display', 1);
            if ($this->_categoryId !== null && Obj::isInt($this->_categoryId) && $this->_categoryId > 0) {
                $records = $records->where('category_id', $this->_categoryId);
            }
            $records = $records->search($this->_terms, null, static::SEARCH_BY_WORDS_COUNT)
                ->take(self::MAX_ITEMS)
                ->get();
            App::$Cache->set('entity.content.search.index.' . $this->_skip, $records, static::CACHE_TIME);
        }

        // lets make active record building
        $this->_records = $records;
        $this->buildContent();
        parent::before();
    }

    /**
     * Build content items as array
     */
    private function buildContent()
    {
        if ($this->_records->count() < 1) {
            return;
        }

        foreach ($this->_records as $item) {
            /** @var \Apps\ActiveRecord\Content $item */
            // full text
            $text = $item->getLocaled('text');
            // remove html
            $text = App::$Security->strip_tags($text);
            // build items
            $this->items[] = [
                'title' => $item->getLocaled('title'),
                'snippet' => Text::snippet($text),
                'uri' => '/content/read/' . $item->getPath(),
                'thumb' => $item->getPosterThumbUri()
            ];
        }
    }



}