<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\Normalize;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Gregwar\Image\Image;

class FormNarrowContentUpdate extends Model
{
    public $title = [];
    public $text = [];
    public $path;
    public $categoryId;
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $poster */
    public $poster;

    public $authorId;

    /** @var Content */
    private $_record;
    /** @var array */
    private $_configs;

    private $_new = false;

    /**
     * FormNarrowContentUpdate constructor. Pass record object inside.
     * @param Content $record
     * @param array $configs
     */
    public function __construct(Content $record, $configs)
    {
        $this->_record = $record;
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
    * Set default values from database record
    */
    public function before()
    {
        // set data from db record
        $this->title = $this->_record->title;
        $this->text = $this->_record->text;
        $this->path = $this->_record->path;
        $this->categoryId = $this->_record->category_id;

        // set current user id
        $this->authorId = App::$User->identity()->getId();
        // set true if it is a new content item
        if ($this->_record->id === null || (int)$this->_record->id < 1) {
            $this->_new = true;
        }

        // set random path slug if not defined
        if ($this->path === null || Str::likeEmpty($this->path)) {
            $randPath = date('d-m-Y') . '-' . Str::randomLatin(mt_rand(8,12));
            $this->path = Str::lowerCase($randPath);
        }
    }

    /**
     * Form field input sources: post/get/file
     * {@inheritDoc}
     * @see \Ffcms\Core\Arch\Model::sources()
     */
    public function sources()
    {
        return [
            'poster' => 'file',
            'title' => 'post',
            'text' => 'post',
            'path' => 'post',
            'categoryId' => 'post'
        ];
    }

    /**
    * Form labels
    * @return array
    */
    public function labels()
    {
        return [
            'title' => __('Title'),
            'text' => __('Text'),
            'path' => __('Path slug'),
            'categoryId' => __('Category'),
            'poster' => __('Poster')
        ];
    }

    /**
    * Content update form validation rules
    * @return array
    */
    public function rules()
    {
        $r = [
            [['path', 'categoryId'], 'required'],
            ['title.' . App::$Request->getLanguage(), 'required'],
            ['text.' . App::$Request->getLanguage(), 'required', null, true, true],
            ['text', 'used', null, true, true],
            ['path', 'direct_match', '/^[a-zA-Z0-9\-]+$/'],
            ['categoryId', 'in', $this->categoryIds()],
            ['path', 'Apps\Model\Front\Content\FormNarrowContentUpdate::validatePath'],
            ['poster', 'used'],
            ['poster', 'isFile', ['jpg', 'png', 'gif', 'jpeg']],
            ['poster', 'sizeFile', (int)$this->_configs['gallerySize'] * 1024] // in bytes
        ];

        foreach (App::$Properties->get('languages') as $lang) {
            $r[] = ['title.' . $lang, 'length_max', 120, null, true, true];
            $r[] = ['keywords.' . $lang, 'length_max', 150];
            $r[] = ['description.' . $lang, 'length_max', 250];
        }

        return $r;
    }

    /**
     * Set attribute validation types
     * @return array
     */
    public function types()
    {
        return [
            'text' => 'html'
        ];
    }

    /**
     * Save input data to database
     */
    public function make()
    {
        // save data to db
        $this->_record->title = $this->title;
        $this->_record->text = $this->text;
        $this->_record->path = $this->path;
        $this->_record->category_id = (int)$this->categoryId;
        $this->_record->display = 0; // set to premoderation
        $this->_record->author_id = (int)$this->authorId;
        if ($this->_new === true) {
            $this->_record->comment_hash = $this->generateCommentHash();
        }

        $this->_record->save();

        // work with poster data
        if ($this->poster !== null) {
            // lets move poster from tmp to gallery
            $originDir = '/upload/gallery/' . $this->_record->id . '/orig/';
            $thumbDir = '/upload/gallery/' . $this->_record->id . '/thumb/';
            if (!Directory::exist($originDir)) {
                Directory::create($originDir);
            }
            if (!Directory::exist($thumbDir)) {
                Directory::create($thumbDir);
            }

            $fileName = App::$Security->simpleHash($this->poster->getClientOriginalName() . $this->poster->getSize());
            $newFullName = $fileName . '.' . $this->poster->guessExtension();
            // move poster to upload gallery directory
            $this->poster->move(Normalize::diskFullPath($originDir), $newFullName);
            // initialize image resizer
            $thumb = new Image();
            $thumb->setCacheDir(root . '/Private/Cache/images');

            // open original file, resize it and save
            $thumbSaveName = Normalize::diskFullPath($thumbDir) . '/' . $fileName . '.jpg';
            $thumb->open(Normalize::diskFullPath($originDir) . DIRECTORY_SEPARATOR . $newFullName)
                ->cropResize($this->_configs['galleryResize'])
                ->save($thumbSaveName, 'jpg', 90);
            $thumb = null;

            // update poster in database
            $this->_record->poster = $newFullName;
            $this->_record->save();
        }
    }

    /**
     * Get allowed category ids as array
     * @return array
     */
    public function categoryIds()
    {
        $data = ContentCategory::getSortedCategories();
        return array_keys($data);
    }

    /**
     * Validate content item pathway
     * @return bool
     */
    public function validatePath()
    {
        // try to find this item
        $find = Content::where('path', '=', $this->path);
        // exclude self id
        if ($this->_record->id !== null && Obj::isLikeInt($this->_record->id)) {
            $find->where('id', '!=', $this->_record->id);
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