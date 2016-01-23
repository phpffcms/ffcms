<?php

namespace Apps\Model\Front\Content;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Apps\ActiveRecord\Content as ContentEntity;

class EntityContentSearch extends Model
{
    const MAX_ITEMS = 5;
    const MIN_QUERY_LENGTH = 2;

    public $items;

    private $_terms;
    private $_skip = [0];
    private $_records;

    /**
     * EntityContentSearch constructor. Pass search terms (query string) to model and used items to skip it by id.
     * @param $terms
     * @param int|array $skipIds
     */
    public function __construct($terms, $skipIds = 0)
    {
        $this->_terms = App::$Security->strip_tags(trim($terms, ' '));
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

        // lets make active record building
        $this->_records = ContentEntity::whereNotIn('id', $this->_skip)
            ->search($this->_terms)
            ->take(self::MAX_ITEMS)
            ->get();
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
            $text = Serialize::getDecodeLocale($item->text);
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