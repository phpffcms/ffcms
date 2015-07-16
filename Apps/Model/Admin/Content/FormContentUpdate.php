<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Integer;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\String;

class FormContentUpdate extends Model
{
    public $title = [];
    public $text = [];
    public $path;
    public $categoryId;
    public $authorId;
    public $keywords = [];
    public $description = [];
    public $display = '1';
    public $source;
    public $addRating = 0;
    public $createdAt;

    public $galleryFreeId;


    private $_content;
    private $_new = false;

    public function __construct(Content $content)
    {
        $this->_content = $content;
        parent::__construct();
    }

    /**
    * Set model properties from active record data
    */
    public function before()
    {
        if ($this->_content->id === null) {
            $this->_new = true;
            if (null === $this->galleryFreeId) {
                $this->galleryFreeId = '_tmp_' . String::randomLatin(mt_rand(16, 32));
            }
            if (null === $this->authorId) {
                $this->authorId = App::$User->identity()->getId();
            }
            if (null === $this->categoryId) {
                $this->categoryId = 1;
            }
        } else {
            $this->title = Serialize::decode($this->_content->title);
            $this->text = Serialize::decode($this->_content->text);
            $this->path = $this->_content->path;
            $this->categoryId = $this->_content->category_id;
            $this->authorId = $this->_content->author_id;
            $this->keywords = Serialize::decode($this->_content->keywords);
            $this->description = Serialize::decode($this->_content->description);
            $this->display = $this->_content->display;
            $this->source  = $this->_content->source;
            $this->createdAt = Date::convertToDatetime($this->_content->created_at, Date::FORMAT_TO_HOUR);
            $this->galleryFreeId = $this->_content->id;
        }
    }

    /**
     * Validation rules
     */
    public function rules()
    {
        $res =  [
            ['title.' . App::$Request->getLanguage(), 'required'],
            ['text.' . App::$Request->getLanguage(), 'required', null, true, true],
            ['text', 'used', null, true, true],
            ['path', 'reverse_match', '/[\/\'~`\!@#\$%\^&\*\(\)+=\{\}\[\]\|;:"\<\>,\?\\\]/'],
            [['path', 'categoryId', 'authorId', 'display', 'galleryFreeId', 'title'], 'required'],
            [['keywords', 'description', 'source', 'addRating', 'createdAt'], 'used'],
            [['addRating', 'authorId', 'display'], 'int'],
            ['display', 'in', ['0', '1']],
            ['categoryId', 'in', $this->categoryIds()],
            ['path', '\Apps\Model\Admin\Content\FormContentUpdate::validatePath'],
            ['authorId', '\App::$User::isExist']
        ];

        foreach (App::$Property->get('languages') as $lang) {
            $res[] = ['title.' . $lang, 'length_max', 120, null, true, true];
            $res[] = ['keywords.' . $lang, 'length_max', 150];
            $res[] = ['description.' . $lang, 'length_max', 250];
        }

        return $res;
    }

    /**
    * Labels
    */
    public function labels()
    {
        return [
            'title' => __('Content title'),
            'text' => __('Content text'),
            'path' => __('Path slug'),
            'categoryId' => __('Category'),
            'keywords' => __('Keywords'),
            'description' => __('Description'),
            'display' => __('Public display'),
            'createdAt' => __('Publish date'),
            'authorId' => __('Author identity'),
            'source' => __('Source URL'),
            'addRating' => __('Change rating')
        ];
    }

    public function save()
    {
        $this->_content->title = Serialize::encode(App::$Security->strip_tags($this->title));
        $this->_content->text = Serialize::encode($this->text);
        $this->_content->path = $this->path;
        $this->_content->category_id = $this->categoryId;
        $this->_content->author_id = $this->authorId;
        $this->_content->display = $this->display;
        $this->_content->keywords = Serialize::encode(App::$Security->strip_tags($this->keywords));
        $this->_content->description = Serialize::encode(App::$Security->strip_tags($this->description));
        $this->_content->source = App::$Security->strip_tags($this->source);
        // check if rating is changed
        if ((int)$this->addRating !== 0) {
            $this->_content->rating += (int)$this->addRating;
        }
        // check if special comment hash is exist
        if ($this->_new || String::length($this->_content->comment_hash) < 32) {
            $this->_content->comment_hash = $this->generateCommentHash();
        }
        // check if date is updated
        if (!String::likeEmpty($this->createdAt) && !String::startsWith('0000', Date::convertToDatetime($this->createdAt, Date::FORMAT_SQL_TIMESTAMP))) {
            $this->_content->created_at = Date::convertToDatetime($this->createdAt, Date::FORMAT_SQL_TIMESTAMP);
        }
        $this->_content->save();
    }

    /**
     * Get allowed category ids as array (string values for validation)
     * @return array
     */
    public function categoryIds()
    {
        $data = ContentCategory::getSortedCategories();
        $response = [];
        foreach ($data as $key=>$val) {
            $response[] = (string)$key;
        }
        return $response;
    }

    /**
     * Validate path filter
     * @return bool
     */
    public function validatePath()
    {
        // try to find this item
        $find = Content::where('path', '=', $this->path);
        // exclude self id
        if ($this->_content->id !== null && Object::isInt($this->_content->id)) {
            $find->where('id', '!=', $this->_content->id);
        }
        // limit only current category id
        $find->where('category_id', '=', $this->categoryId);

        return $find->count() < 1;
    }

    /**
     * Generate random string for comment hash value
     * @return string
     */
    private function generateCommentHash()
    {
        $hash = String::randomLatinNumeric(mt_rand(32, 128));
        $find = Content::where('comment_hash', '=', $hash)->count();
        // hmmm, is always exist? Chance of it is TOOOO low, but lets recursion re-generate
        if ($find !== 0) {
            return $this->generateCommentHash();
        }

        return $hash;
    }
}