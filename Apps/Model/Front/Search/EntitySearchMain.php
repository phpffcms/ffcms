<?php

namespace Apps\Model\Front\Search;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\HTML\System\Dom;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

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
     * Try to search in classic apps
     */
    public function make()
    {
        $itemCount = (int)$this->_configs['itemPerApp'];
        // search content items
        $content = new SearchContent($this->query, $itemCount);
        $this->results['Content'] = $content->getResult();
        // search comments
        $comments = new SearchComments($this->query, $itemCount);
        $this->results['Comments'] = $comments->getResult();
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

        // sort output by relevance
        krsort($result);

        // return result as array
        return $result;
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
            $highlight = $dom->{$tag}(function() use ($query) {
                return $query;
            }, $properties);
            $text = Str::ireplace($query, $highlight, $text);
        }
        return $text;
    }


}