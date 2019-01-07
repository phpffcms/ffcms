<?php

namespace Apps\Model\Front\Search\Interfaces;

interface SearchCollection
{
    /**
     * SearchContainer constructor. Pass string query inside
     * @param string $query
     * @param int $limit
     */
    public function __construct($query, $limit);

    /**
     * Build search results. Should return array collection: AbstractSearchResult[]
     * @return array
     */
    public function getResult();
}
