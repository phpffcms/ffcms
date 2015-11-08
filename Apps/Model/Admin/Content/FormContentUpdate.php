<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Integer;
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;

class FormContentUpdate extends Model
{
    public $title = [];
    public $text = [];
    public $path;
    public $poster;
    public $categoryId;
    public $authorId;
    public $metaTitle;
    public $metaKeywords = [];
    public $metaDescription = [];
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
        // is new item?
        if ($this->_content->id === null) {
            $this->_new = true;
            if (null === $this->galleryFreeId) {
                $this->galleryFreeId = '_tmp_' . Str::randomLatin(mt_rand(16, 32));
            }
            if (null === $this->authorId) {
                $this->authorId = App::$User->identity()->getId();
            }
            if (null === $this->categoryId) {
                $this->categoryId = 1;
            }
        } else { // is edit of exist item? define available data
            $this->title = Serialize::decode($this->_content->title);
            $this->text = Serialize::decode($this->_content->text);
            $this->path = $this->_content->path;
            $this->poster = $this->_content->poster;
            $this->categoryId = $this->_content->category_id;
            $this->authorId = $this->_content->author_id;
            $this->metaTitle = Serialize::decode($this->_content->meta_title);
            $this->metaKeywords = Serialize::decode($this->_content->meta_keywords);
            $this->metaDescription = Serialize::decode($this->_content->meta_description);
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
            [['metaTitle', 'metaKeywords', 'metaDescription', 'poster', 'source', 'addRating', 'createdAt'], 'used'],
            [['addRating', 'authorId', 'display'], 'int'],
            ['display', 'in', ['0', '1']],
            ['categoryId', 'in', $this->categoryIds()],
            ['path', '\Apps\Model\Admin\Content\FormContentUpdate::validatePath'],
            ['authorId', '\App::$User::isExist']
        ];

        foreach (App::$Properties->get('languages') as $lang) {
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
            'metaTitle' => __('Meta title'),
            'metaKeywords' => __('Meta keywords'),
            'metaDescription' => __('Meta description'),
            'display' => __('Public display'),
            'createdAt' => __('Publish date'),
            'authorId' => __('Author identity'),
            'source' => __('Source URL'),
            'addRating' => __('Change rating'),
            'poster' => __('Poster')
        ];
    }

    /**
     * Save changes in database
     */
    public function save()
    {
        $this->_content->title = Serialize::encode(App::$Security->strip_tags($this->title));
        $this->_content->text = Serialize::encode($this->text);
        $this->_content->path = $this->path;
        $this->_content->category_id = $this->categoryId;
        $this->_content->author_id = $this->authorId;
        $this->_content->display = $this->display;
        $this->_content->meta_title = Serialize::encode(App::$Security->strip_tags($this->metaTitle));
        $this->_content->meta_keywords = Serialize::encode(App::$Security->strip_tags($this->metaKeywords));
        $this->_content->meta_description = Serialize::encode(App::$Security->strip_tags($this->metaDescription));
        $this->_content->source = App::$Security->strip_tags($this->source);
        // check if rating is changed
        if ((int)$this->addRating !== 0) {
            $this->_content->rating += (int)$this->addRating;
        }
        // check if special comment hash is exist
        if ($this->_new || Str::length($this->_content->comment_hash) < 32) {
            $this->_content->comment_hash = $this->generateCommentHash();
        }
        // check if date is updated
        if (!Str::likeEmpty($this->createdAt) && !Str::startsWith('0000', Date::convertToDatetime($this->createdAt, Date::FORMAT_SQL_TIMESTAMP))) {
            $this->_content->created_at = Date::convertToDatetime($this->createdAt, Date::FORMAT_SQL_TIMESTAMP);
        }

        // save poster data
        $posterPath = '/upload/gallery/' . $this->galleryFreeId . '/orig/' . $this->poster;
        if (File::exist($posterPath)) {
            $this->_content->poster = $this->poster;
        }

        // get temporary gallery id
        $tmpGalleryId = $this->galleryFreeId;

        // save row
        $this->_content->save();

        // move files
        if ($tmpGalleryId !== $this->_content->id) {
            Directory::rename('/upload/gallery/' . $tmpGalleryId, $this->_content->id);
        }
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
        if ($this->_content->id !== null && Object::isLikeInt($this->_content->id)) {
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
        $hash = Str::randomLatinNumeric(mt_rand(32, 128));
        $find = Content::where('comment_hash', '=', $hash)->count();
        // hmmm, is always exist? Chance of it is TOOOO low, but lets recursion re-generate
        if ($find !== 0) {
            return $this->generateCommentHash();
        }

        return $hash;
    }
}