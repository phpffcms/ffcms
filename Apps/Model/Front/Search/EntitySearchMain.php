<?php

namespace Apps\Model\Front\Search;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Obj;

class EntitySearchMain extends Model
{
    const ITEM_PER_APP = 10;

    public $results = [];
    public $query;

    private $_configs;

    /**
     * EntitySearchMain constructor. Pass query inside
     * @param string $query
     * @param array|null $configs
     */
    public function __construct($query, array $configs = null)
    {
        $this->query = $query;
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Try to search in classic apps
     */
    public function make()
    {
        // search content items
        $content = new SearchContent($this->query, (int)$this->_configs['itemPerApp']);
        $this->results['Content'] = $content->getResult();
    }

    /**
     * Get sorted by relevance search response. Method return result as array: [relevance => [title, snippet, uri, date], ...]
     * @return array
     */
    public function getRelevanceSortedResult()
    {
        $result = [];
        // each every content type
        foreach ($this->results as $type => $items) {
            if (!Obj::isArray($items)) {
                continue;
            }
            // each every element
            foreach ($items as $item) {
                /** @var AbstractSearchResult $item */
                // build unique relevance. Problem: returned relevance from query is integer
                // and can be duplicated. So, we add random complex float value and make it string to sort in feature
                $uniqueRelevance = (string)($item->getRelevance() + (mt_rand(0, 999)/10000));
                // build response
                $result[$uniqueRelevance] = [
                    'title' => $item->getTitle(),
                    'snippet' => $item->getSnippet(),
                    'uri' => $item->getUri(),
                    'date' => $item->getDate()
                ];
            }
        }

        ksort($result);

        return $result;
    }


}