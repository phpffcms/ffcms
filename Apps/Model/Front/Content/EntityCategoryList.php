<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\Content as ContentRecord;
use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/**
 * Class EntityCategoryList. Build content and category data to display in views based on pathway.
 * @package Apps\Model\Front\Content
 */
class EntityCategoryList extends Model
{
    // properties to display: content item collection, category data, etc
    public $items;
    public $category;
    public $categories;

    // private items used on model building
    private $_path;
    private $_configs;
    private $_page = 0;
    private $_sort;
    private $_contentCount = 0;

    private $_currentCategory;
    private $_allCategories;
    private $_catIds;

    /** @var bool|int */
    private $_customItemLimit = false;

    /**
     * EntityCategoryList constructor. Pass pathway as string and data of multi-category system
     * @param string $path
     * @param array $configs
     * @param int $offset
     * @param string $sort
     */
    public function __construct($path, array $configs, $offset = 0, $sort = 'newest')
    {
        $this->_path = $path;
        $this->_configs = $configs;
        $this->_page = (int)$offset;
        $this->_sort = $sort;
        parent::__construct();
    }

    /**
     * Build model properties
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function before()
    {
        // find one or more categories where we must looking for content items
        if ((int)$this->_configs['multiCategories'] === 1) {
            $this->findCategories();
        } else {
            $this->findCategory();
        }

        // try to find content items depend of founded category(ies)
        $records = $this->findItems();
        // build output information
        $this->buildCategory();
        $this->buildContent($records);
    }

    /**
     * Set select items limit count
     * @param int $limit
     */
    public function setItemLimit($limit)
    {
        $this->_customItemLimit = (int)$limit;
    }

    /**
     * Find current category data
     * @throws NotFoundException
     */
    private function findCategory()
    {
        // get current category
        $query = ContentCategory::where('path', '=', $this->_path);
        if ($query->count() !== 1) {
            throw new NotFoundException(__('Category is not founded'));
        }

        // set properties from query
        $this->_allCategories = $query->get();
        $this->_currentCategory = $query->first();
        $this->_catIds[] = $this->_currentCategory['id'];
    }

    /**
     * Find multiple categories child of current
     * @throws NotFoundException
     */
    private function findCategories()
    {
        // get all categories for current path and child of it
        $query = ContentCategory::where('path', 'like', $this->_path . '%');
        if ($query->count() < 1) {
            throw new NotFoundException(__('Category is not founded'));
        }
        // get result as object
        $result = $query->get();

        // prevent to select hidden categories
        foreach ($result as $key => $obj) {
            if ((int)$obj->configs['showCategory'] !== 1) {
                unset($result[$key]);
            }
        }

        // extract ids from result as array by key id
        $this->_catIds = Arr::pluck('id', $result->toArray());

        // get current category matching
        foreach ($result as $row) {
            if ($row->path === $this->_path) {
                $this->_currentCategory = $row;
            }
        }

        // set result to property
        $this->_allCategories = $result;
    }

    /**
     * Find content items on database and return rows as object
     * @return mixed
     * @throws NotFoundException
     */
    private function findItems()
    {
        if (!Any::isArray($this->_catIds) || count($this->_catIds) < 1) {
            throw new NotFoundException(__('Category is not founded'));
        }

        // calculate selection offset
        $itemPerPage = (int)$this->_configs['itemPerCategory'];
        // check if custom itemlimit defined over model api
        if ($this->_customItemLimit !== false) {
            $itemPerPage = (int)$this->_customItemLimit;
        }

        $offset = $this->_page * $itemPerPage;

        // get all items from categories
        $query = ContentRecord::with(['user', 'user.profile'])
            ->whereIn('category_id', $this->_catIds)
            ->where('display', '=', 1);
        // save count
        $this->_contentCount = $query->count();

        // apply sort by
        switch ($this->_sort) {
            case 'rating':
                $query = $query->orderBy('rating', 'DESC');
                break;
            case 'views':
                $query = $query->orderBy('views', 'DESC');
                break;
            default:
                $query = $query->orderBy('important', 'DESC')->orderBy('created_at', 'DESC');
                break;
        }

        // get all items if offset is negative
        if ($itemPerPage < 0) {
            return $query->get();
        }

        // make select based on offset
        return $query->skip($offset)->take($itemPerPage)->get();
    }

    /**
     * Prepare category data to display
     * @throws ForbiddenException
     */
    private function buildCategory()
    {
        $catConfigs = $this->_currentCategory->configs;
        // prepare rss url link for current category if enabled
        $rssUrl = false;
        if ((int)$this->_configs['rss'] === 1 && (int)$catConfigs['showRss'] === 1) {
            $rssUrl = App::$Alias->baseUrl . '/content/rss/' . $this->_currentCategory->path;
            $rssUrl = rtrim($rssUrl, '/');
        }

        // prepare sorting urls
        $catSortParams = [];
        if (App::$Request->query->get('page')) {
            $catSortParams['page'] = (int)App::$Request->query->get('page');
        }

        $catSortUrls = [
            'views' => Url::to('content/list', [$this->_currentCategory->path], Arr::merge($catSortParams, ['sort' => 'views'])),
            'rating' => Url::to('content/list', [$this->_currentCategory->path], Arr::merge($catSortParams, ['sort' => 'rating'])),
            'newest' => Url::to('content/list', [$this->_currentCategory->path], $catSortParams)
        ];

        // prepare current category data to output (unserialize locales and strip tags)
        $this->category = [
            'title' => $this->_currentCategory->getLocaled('title'),
            'description' => $this->_currentCategory->getLocaled('description'),
            'configs' => $catConfigs,
            'path' => $this->_currentCategory->path,
            'rss' => $rssUrl,
            'sort' => $catSortUrls
        ];

        // check if this category is hidden
        if (!(bool)$this->category['configs']['showCategory']) {
            throw new ForbiddenException(__('This category is not available to view'));
        }

        // make sorted tree of categories to display in breadcrumbs
        foreach ($this->_allCategories as $cat) {
            $this->categories[$cat->id] = $cat;
        }
    }

    /**
     * Build content data to model properties
     * @param $records
     * @throws NotFoundException
     */
    private function buildContent($records)
    {
        $nullItems = 0;
        foreach ($records as $row) {
            /** @var Content $row */
            // check title length on current language locale
            $localeTitle = $row->getLocaled('title');
            if (Str::likeEmpty($localeTitle)) {
                ++$nullItems;
                continue;
            }

            // get snippet from full text for current locale
            $text = Text::snippet($row->getLocaled('text'));

            $itemPath = $this->categories[$row->category_id]->path;
            if (!Str::likeEmpty($itemPath)) {
                $itemPath .= '/';
            }
            $itemPath .= $row->path;

            // prepare tags data
            $tags = $row->getLocaled('meta_keywords');
            if (!Str::likeEmpty($tags)) {
                $tags = explode(',', $tags);
            } else {
                $tags = null;
            }

            $owner = $row->user;
            // make a fake if user is not exist over id
            if (!$owner) {
                $owner = new User();
            }

            // check if current user can rate item
            $ignoredRate = App::$Session->get('content.rate.ignore');
            $canRate = true;
            if (Any::isArray($ignoredRate) && Arr::in((string)$row->id, $ignoredRate)) {
                $canRate = false;
            }

            if (!App::$User->isAuth()) {
                $canRate = false;
            } elseif ($owner->getId() === App::$User->identity()->getId()) { // own item
                $canRate = false;
            }

            // build result array
            $this->items[] = [
                'id' => $row->id,
                'title' => $localeTitle,
                'text' => $text,
                'date' => Date::humanize($row->created_at),
                'updated' => $row->updated_at,
                'author' => $owner,
                'poster' => $row->getPosterUri(),
                'thumb' => $row->getPosterThumbUri(),
                'thumbSize' => File::size($row->getPosterThumbUri()),
                'views' => (int)$row->views,
                'rating' => (int)$row->rating,
                'canRate' => $canRate,
                'category' => $this->categories[$row->category_id],
                'uri' => '/content/read/' . $itemPath,
                'tags' => $tags,
                'important' => (bool)$row->important
            ];
        }

        if ($nullItems === $this->_contentCount) {
            throw new NotFoundException(__('Content is not founded'));
        }
    }

    /**
     * Get content items count
     * @return int
     */
    public function getContentCount()
    {
        return $this->_contentCount;
    }
}
