<?php

namespace Apps\Model\Front\Search;

use Ffcms\Core\Helper\Date;

/**
 * Class AbstractSearchResult. Static setter & getter instance to organize search result items.
 * Yes, i know it's a fully sh@t for php, but this is most useful when extending model should follow union format.
 * Maybe interface and __magic is better, you can suggest it on github ;)
 * @package Apps\Model\Front\Search
 */
class AbstractSearchResult
{
    protected $title;
    protected $snippet;
    protected $uri;
    protected $date;
    protected $relevance;

    /**
     * Set item title
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Get item title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set item snippet
     * @param string $value
     */
    public function setSnippet($value)
    {
        $this->snippet = $value;
    }

    /**
     * Get item snippet
     * @return string|null
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * Set item uri path
     * @param string $value
     */
    public function setUri($value)
    {
        $this->uri = $value;
    }

    /**
     * Get item path
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set item date
     * @param string $value
     */
    public function setDate($value)
    {
        $this->date = Date::humanize($value);
    }

    /**
     * Get item date
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set item relevance
     * @param int|float $value
     */
    public function setRelevance($value)
    {
        $this->relevance = $value;
    }

    /**
     * Get item relevance
     * @return float|int
     */
    public function getRelevance()
    {
        return $this->relevance;
    }
}
