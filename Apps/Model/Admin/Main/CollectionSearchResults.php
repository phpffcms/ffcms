<?php

namespace Apps\Model\Admin\Main;


use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Helper\Html\Dom;

/**
 * Class CollectionSearchResults. Search collection for results with some features
 * @package Apps\Model\Admin\Main
 */
class CollectionSearchResults
{
    /** @var AbstractSearchItem[]|null */
    private $results;

    private $query;
    private $limit;

    /**
     * CollectionSearchResults constructor.
     * @param string $query
     * @param int $limit
     */
    public function __construct(string $query, int $limit = 10)
    {
        $this->query = $query;
        $this->limit = $limit;
    }

    /**
     * Add search result item
     * @param AbstractSearchItem $item
     */
    public function add(AbstractSearchItem $item): void
    {
        $this->results[] = $item;
    }

    /**
     * Get search query
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Get result limit
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Get sorted by relevance search response. Method return result as array: [relevance => [title, snippet, uri, date], ...]
     * @return AbstractSearchItem[]
     */
    public function getRelevanceBasedResult(): ?array
    {
        if (!$this->results) {
            return null;
        }

        $result = [];
        // each every content type
        foreach ($this->results as $item) {
            /** @var AbstractSearchItem $item */
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
     * Highlight words in text by current query request.
     * @param string $text
     * @param string $tag
     * @param array $properties
     * @return string
     */
    public function highlightText($text, $tag, array $properties = []): ?string
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