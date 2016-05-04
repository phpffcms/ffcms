<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Type\Obj;

class EntityContentRead extends Model
{
    public $id;
    public $title;
    public $path;
    public $text;
    public $createDate;
    public $editDate;
    public $catName;
    public $catPath;
    public $authorId;
    public $authorName;
    public $views;
    public $catNesting = [];
    public $source;
    public $posterThumb;
    public $posterFull;
    public $rating;
    public $canRate;

    public $metaTitle;
    public $metaDescription;
    public $metaKeywords;

    // gallery image key-value array as thumb->full
    public $galleryItems;

    // private activerecord relation objects
    private $_category;
    private $_content;

    /**
     * Pass active record objects
     * @param ContentCategory $category
     * @param Content $content
     */
    public function __construct(ContentCategory $category, Content $content)
    {
        $this->_category = $category;
        $this->_content = $content;
        parent::__construct();
    }

    /**
     * Prepare model attributes from passed objects
     * @throws ForbiddenException
    */
    public function before()
    {
        $this->id = $this->_content->id;
        $this->title = Serialize::getDecodeLocale($this->_content->title);
        $this->text = Serialize::getDecodeLocale($this->_content->text);

        // check if title and text are exists
        if (Str::length($this->title) < 1 || Str::length($this->text) < 1) {
            throw new ForbiddenException();
        }

        // get meta data
        $this->metaTitle = Serialize::getDecodeLocale($this->_content->meta_title);
        if (Str::likeEmpty($this->metaTitle)) {
            $this->metaTitle = $this->title;
        }
        $this->metaDescription = Serialize::getDecodeLocale($this->_content->meta_description);
        $tmpKeywords = Serialize::getDecodeLocale($this->_content->meta_keywords);
        $this->metaKeywords = explode(',', $tmpKeywords);

        // set content date, category data
        $this->createDate = Date::humanize($this->_content->created_at);
        $this->catName = Serialize::getDecodeLocale($this->_category->title);
        $this->catPath = $this->_category->path;

        // set user data
        if (App::$User->isExist($this->_content->author_id)) {
            $this->authorId = $this->_content->author_id;
            $profile = App::$User->identity($this->authorId)->getProfile();
            $this->authorName = $profile->getNickname();
        }

        $this->source = $this->_content->source;
        $this->views = $this->_content->views+1;
        // check for dependence, add '' for general cat, ex: general/depend1/depend2/.../depend-n
        $catNestingArray = Arr::merge([0 => ''], explode('/', $this->catPath));
        if ($catNestingArray > 1) {
            // latest element its a current nesting level, lets cleanup it
            array_pop($catNestingArray);
            $catNestingPath = null;
            foreach ($catNestingArray as $cPath) {
                $catNestingPath .= $cPath;

                // try to find category by path in db
                $record = ContentCategory::getByPath($catNestingPath);
                if ($record !== null && $record->count() > 0) {
                    // if founded - add to nesting data
                    $this->catNesting[] = [
                        'name' => Serialize::getDecodeLocale($record->title),
                        'path' => $record->path
                    ];
                }
                if (!Str::likeEmpty($catNestingPath)) {
                    $catNestingPath .= '/';
                }
            }
        }

        // build array of category nesting level
        $this->catNesting[] = [
            'name' => $this->catName,
            'path' => $this->catPath
        ];

        // get gallery images and poster data
        $galleryPath = '/upload/gallery/' . $this->_content->id;
        // check if gallery folder is exist
        if (Directory::exist($galleryPath)) {
            $originImages = File::listFiles($galleryPath . '/orig/', ['.jpg', '.png', '.gif', '.jpeg', '.bmp', '.webp'], true);
            // generate poster data
            if (Arr::in($this->_content->poster, $originImages)) {
                // original poster
                $posterName = $this->_content->poster;
                $this->posterFull = $galleryPath . '/orig/' . $posterName;
                if (!File::exist($this->posterFull)) {
                    $this->posterFull = null;
                }
                // thumb poster
                $posterSplit = explode('.', $posterName);
                array_pop($posterSplit);
                $posterCleanName = implode('.', $posterSplit);
                $this->posterThumb = $galleryPath . '/thumb/' . $posterCleanName . '.jpg';
                if (!File::exist($this->posterThumb)) {
                    $this->posterThumb = null;
                }
            }

            // generate full gallery
            foreach ($originImages as $image) {
                $imageSplit = explode('.', $image);
                array_pop($imageSplit);
                $imageClearName = implode('.', $imageSplit);
                // skip image used in poster
                if (Str::startsWith($imageClearName, $this->_content->poster)) {
                    continue;
                }
                $thumbPath = $galleryPath . '/thumb/' . $imageClearName . '.jpg';
                if (File::exist($thumbPath)) {
                    $this->galleryItems[$thumbPath] = $galleryPath . '/orig/' . $image;
                }
            }
        }
        
        // set rating data
        $this->rating = $this->_content->rating;
        $ignoredRate = App::$Session->get('content.rate.ignore');
        $this->canRate = true;
        if (Obj::isArray($ignoredRate) && Arr::in((string)$this->id, $ignoredRate)) {
            $this->canRate = false;
        }
        if (!App::$User->isAuth()) {
            $this->canRate = false;
        } elseif ($this->authorId === App::$User->identity()->getId()) {
            $this->canRate = false;
        }
        
        // update views count
        $this->_content->views += 1;
        $this->_content->save();
    }

    /**
     * Get category relation of this content
     * @return ContentCategory
     */
    public function getCategory()
    {
        return $this->_category;
    }
}