<?php


namespace Apps\Model\Front\Search;


use Apps\ActiveRecord\Content;
use Apps\Model\Front\Search\Interfaces\SearchContainer;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class SearchContent. Search instance model for content.
 * @package Apps\Model\Front\Search
 */
class SearchContent extends Model implements SearchContainer
{
    private $query;
    private $limit;

    /**
     * SearchContainer constructor. Pass string query inside
     * @param string $query
     * @param int $limit
     */
    public function __construct($query, $limit = 10)
    {
        $this->query = $query;
        $this->limit = (int)$limit;
        if ($this->limit < 1) {
            $this->limit = 1;
        }
        parent::__construct();
    }

    /**
     * Build search results
     * @return array[AbstractSearchResult]
     */
    public function getResult()
    {
        // relevant search by string query
        $records = Content::where('display', '=', 1)
            ->search($this->query)
            ->take($this->limit)
            ->get();

        // check if result is not empty
        if ($records->count() < 1) {
            return [];
        }

        // build result items
        $result = [];
        foreach ($records as $item) {
            /** @var \Apps\ActiveRecord\Content $item */
            $title = $item->getLocaled('title');
            $text = App::$Security->strip_tags($item->getLocaled('text'));
            $snippet = Text::snippet($text);
            // prevent empty items
            if (Str::likeEmpty($title)) {
                continue;
            }

            // initialize abstract response pattern
            $res = new AbstractSearchResult();
            $res->setTitle($title);
            $res->setSnippet($snippet);
            $res->setDate($item->created_at);
            $res->setRelevance((int)$item->relevance);
            $res->setUri('/content/read/' . $item->getPath());

            // accumulate response var
            $result[] = $res;
        }
        
        return $result;
    }
}