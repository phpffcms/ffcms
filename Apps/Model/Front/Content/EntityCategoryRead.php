<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Exception\NotFoundException;

class EntityCategoryRead extends Model
{
    const PAGE_BREAK = '<div style="page-break-after: always">';

    public $items;
    public $categoryData;

    /** @var object $_records */
    public $_records;
    /** @var array|null $_category */
    public $_category;
    /** @var array|null $_allCategories */
    public $_allCategories;

    private $_nullCount;

    /**
     * Pass data from initialization
     * @param object $records
     * @param array|null $category
     * @param array|null $dependCategories
     */
    public function __construct($records, array $category = null, array $dependCategories = null)
    {
        $this->_records = $records;
        $this->_category = $category;
        $this->_allCategories = $dependCategories;
        parent::__construct();
    }

    /**
    * Prepare passed data in __construct
    * @throws ForbiddenException
    * @throws NotFoundException
    */
    public function before()
    {
        $this->categoryData = [
            'title' => App::$Security->strip_tags(Serialize::getDecodeLocale($this->_category['title'])),
            'description' => App::$Security->strip_tags(Serialize::getDecodeLocale($this->_category['description'])),
            'configs' => Serialize::decode($this->_category['configs']),
            'path' => $this->_category['path']
        ];

        // check if this category is hidden
        if ((int)$this->categoryData['configs']['showCategory'] !== 1) {
            throw new ForbiddenException();
        }

        $unsortedCategories = $this->_allCategories;
        $this->_allCategories = [];
        foreach ($unsortedCategories as $cat) {
            if ($this->_allCategories[$cat['id']] === null) {
                $this->_allCategories[$cat['id']] = $cat;
            }
        }

        // build records data
        foreach ($this->_records as $row) {
            // get full text
            $text = Serialize::getDecodeLocale($row->text);
            // try to find page breaker
            $breakPosition = mb_strpos($text, self::PAGE_BREAK, null, 'UTF-8');
            // offset is finded, try to split preview from full text
            if ($breakPosition !== false) {
                $text = Str::substr($text, 0, $breakPosition);
            } else { // page breaker is not founded, lets get a fun ;D
                // find first paragraph ending
                $breakPosition = mb_strpos($text, '</p>', null, 'UTF-8');
                // cut text from position caret before </p> (+4 symbols to save item as valid)
                $text = Str::substr($text, 0, $breakPosition+4);
            }

            $itemPath = $this->_allCategories[$row->category_id]['path'];
            if (!Str::likeEmpty($itemPath)) {
                $itemPath .= '/';
            }
            $itemPath .= $row->path;

            // try to find poster and thumbnail for this content item
            $poster = $row->poster;
            $thumb = null;
            if (!Str::likeEmpty($poster)) {
                $thumbName = Str::cleanExtension($poster) . '.jpg';
                $poster = '/upload/gallery/' . $row->id . '/orig/' . $poster;
                $thumb = '/upload/gallery/' . $row->id . '/thumb/' . $thumbName;
                if (!File::exist($poster)) {
                    $poster = null;
                }
                if (!File::exist($thumb)) {
                    $thumb = null;
                }
            } else {
                $poster = null;
            }

            // prepare tags data
            $tags = Serialize::getDecodeLocale($row->meta_keywords);
            if (!Str::likeEmpty($tags)) {
                $tags = explode(',', $tags);
            } else {
                $tags = null;
            }

            $localeTitle = App::$Security->strip_tags(Serialize::getDecodeLocale($row->title));
            if (Str::length($localeTitle) < 1) {
                ++$this->_nullCount;
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
                'poster' => $poster,
                'thumb' => $thumb,
                'views' => (int)$row->views,
                'rating' => (int)$row->rating,
                'category' => $this->_allCategories[$row->category_id],
                'uri' => '/content/read/' . $itemPath,
                'tags' => $tags
            ];
        }

        // try to check count of null items without content
        if ($this->_nullCount === $this->_records->count()) {
            throw new NotFoundException();
        }
    }
}