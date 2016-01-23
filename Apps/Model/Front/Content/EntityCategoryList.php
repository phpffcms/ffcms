<?php

namespace Apps\Model\Front\Content;


use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\Content as ContentRecord;
use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class EntityCategoryList. Build content and category data to display in views based on pathway.
 * @package Apps\Model\Front\Content
 */
class EntityCategoryList extends Model
{
    // page breaker to split short and full content
    const PAGE_BREAK = '<div style="page-break-after: always">';

    // properties to display: content item collection, category data, etc
    public $items;
    public $category;
    public $categories;

    // private items used on model building
    private $_path;
    private $_configs;
    private $_page = 0;
    private $_contentCount = 0;

    private $_currentCategory;
    private $_allCategories;
    private $_catIds;

    /**
     * EntityCategoryList constructor. Pass pathway as string and data of multi-category system
     * @param string $path
     * @param array $configs
     * @param int $offset
     */
    public function __construct($path, array $configs, $offset = 0)
    {
        $this->_path = $path;
        $this->_configs = $configs;
        $this->_page = (int)$offset;
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
        // prepare output data
        $this->buildOutput($records);
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

        // extract ids from result as array by key id
        $this->_catIds = Arr::ploke('id', $result->toArray());

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
        if (!Obj::isArray($this->_catIds) || count($this->_catIds) < 1) {
            throw new NotFoundException(__('Category is not founded'));
        }

        // calculate selection offset
        $itemPerPage = (int)$this->_configs['itemPerCategory'];
        if ($itemPerPage < 1) {
            $itemPerPage = 1;
        }
        $offset = $this->_page * $itemPerPage;

        // get all items from categories
        $query = ContentRecord::whereIn('category_id', $this->_catIds)
            ->where('display', '=', 1);
        // save count
        $this->_contentCount = $query->count();

        // make select based on offset
        return $query->skip($offset)->take($itemPerPage)->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Build content data to model properties
     * @param $records
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    private function buildOutput($records)
    {
        // prepare current category data to output (unserialize locales and strip tags)
        $this->category = [
            'title' => App::$Security->strip_tags($this->_currentCategory->getLocaled('title')),
            'description' => App::$Security->strip_tags($this->_currentCategory->getLocaled('description')),
            'configs' => Serialize::decode($this->_currentCategory->configs),
            'path' => $this->_currentCategory->path
        ];

        // check if this category is hidden
        if ((int)$this->category['configs']['showCategory'] !== 1) {
            throw new ForbiddenException(__('This category is not available to view'));
        }

        // make sorted tree of categories to display in breadcrumbs
        foreach ($this->_allCategories as $cat) {
            $this->categories[$cat->id] = $cat;
        }

        $nullItems = 0;
        foreach ($records as $row) {
            /** @var Content $row */
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

            // check title length on current language locale
            $localeTitle = App::$Security->strip_tags($row->getLocaled('title'));
            if (Str::length($localeTitle) < 1) {
                ++$nullItems;
            }

            $owner = App::$User->identity($row->author_id);
            // make a fake if user is not exist over id
            if ($owner === null) {
                $owner = new User();
            }

            // build result array
            $this->items[] = [
                'title' => $localeTitle,
                'text' => $text,
                'date' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_HOUR),
                'author' => $owner,
                'poster' => $row->getPosterUri(),
                'thumb' => $row->getPosterThumbUri(),
                'thumbSize' => File::size($row->getPosterThumbUri()),
                'views' => (int)$row->views,
                'rating' => (int)$row->rating,
                'category' => $this->categories[$row->category_id],
                'uri' => '/content/read/' . $itemPath,
                'tags' => $tags
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