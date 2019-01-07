<?php

namespace Apps\Model\Admin\Main;


use Apps\Model\Front\Search\AbstractSearchResult;
use Ffcms\Templex\Url\Url;

/**
 * Class AbstractSearchItem. Search item element instance
 * @package Apps\Model\Admin\Main
 */
class AbstractSearchItem extends AbstractSearchResult
{
    protected $marker = 'Unknown';
    protected $url;

    /**
     * Set item instance type
     * @param string $marker
     */
    public function setMarker(string $marker): void
    {
        $this->marker = $marker;
    }

    /**
     * Get item instance type
     * @return string
     */
    public function getMarker(): string
    {
        return $this->marker;
    }

    /**
     * Build url
     * @param string $controllerAction
     * @param array|null $arguments
     * @param array|null $query
     */
    public function setUrl(string $controllerAction, ?array $arguments = null, ?array $query = null)
    {
        $this->url = Url::to($controllerAction, $arguments, $query);
    }

    /**
     * Get url result
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}