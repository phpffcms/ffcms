<?php

namespace Apps\Model\Front\Search;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Helper\Html\Dom;

/**
 * Class EntitySearchMain. Search everything main business logic model
 * @package Apps\Model\Front\Search
 */
class EntitySearchMain extends Model
{
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
     * Get initialize configs
     * @return array|null
     */
    public function getConfigs(): ?array
    {
        return $this->_configs;
    }

    /**
     * Add result item to main collection
     * @param AbstractSearchResult $result
     */
    public function add(AbstractSearchResult $result)
    {
        $this->results[] = $result;
    }

    /**
     * Get sorted by relevance search response. Method return result as array: [relevance => [title, snippet, uri, date], ...]
     * NO MORE SUPPORTED! ONLY NATIVE FULL TEXT FEATURES.
     * @return array
     * @deprecated
     */
    public function getRelevanceSortedResult()
    {
        $result = [];
        // each every content type
        foreach ($this->results as $item) {
            /** @var AbstractSearchResult $item */
            // build unique relevance. Problem: returned relevance from query is integer
            // and can be duplicated. So, we add random complex float value and make it string to sort in feature
            $uniqueRelevance = (string)($item->getRelevance() + (mt_rand(0, 999) / 10000));
            // build response
            $result[$uniqueRelevance] = $item;
        }

        // sort output by relevance
        krsort($result);

        // return result as array
        return $result;
    }

    /**
     * Get search result
     * @return AbstractSearchResult[]|null
     */
    public function getResult()
    {
        return $this->results;
    }

    /**
     * Highlight words in text by current query request.
     * @param string $text
     * @param string $tag
     * @param array $properties
     * @return string
     */
    public function highlightText($text, $tag, array $properties = [])
    {
        $queries = explode(' ', $this->query);
        $dom = new Dom();
        foreach ($queries as $query) {
            $highlight = $dom->{$tag}(function () use ($query) {
                return $query;
            }, $properties);
            $text = Str::ireplace($query, $highlight, $text);
        }
        return $text;
    }
}
