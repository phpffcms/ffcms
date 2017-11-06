<?php

namespace Apps\Model\Front\Search;


use Apps\ActiveRecord\CommentPost;
use Apps\Model\Front\Search\Interfaces\SearchContainer;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Text;

/**
 * Class SearchComments. Search instance for comments text.
 * @package Apps\Model\Front\Search
 */
class SearchComments extends Model implements SearchContainer
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
     * Build search results. Should return array collection: [AbstractSearchResult]
     * @return array
     */
    public function getResult()
    {
        // search in comments post
        $query = CommentPost::search($this->query)
            ->where('moderate', '=', 0)
            ->take($this->limit)
            ->get();

        // check if response is empty
        if ($query->count() < 1) {
            return [];
        }

        // build output
        $result = [];
        foreach ($query as $item) {
            /** @var CommentPost $item */
            $snippet = App::$Security->strip_tags($item->message);
            $snippet = Text::snippet($snippet);

            // make unique instance object
            $instance = new AbstractSearchResult();
            $instance->setTitle(App::$Translate->get('Search', 'Comment on the page'));
            $instance->setSnippet($snippet);
            $instance->setUri($item->pathway . '#comments-list');
            $instance->setDate($item->created_at);
            $instance->setRelevance((int)$item->relevance);

            // add instance to result set
            $result[] = $instance;
        }

        return $result;
    }
}