<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class EntityContentRead. Prepare record object data to display.
 * @package Apps\Model\Front\Content
 */
class EntityContentRead extends Model
{
    public $id;
    public $title;
    public $path;
    public $text;
    public $display;
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

    // private ActiveRecord relation objects
    private $_category;
    private $_content;

    /**
     * EntityContentRead constructor. Pass active record objects
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
        // set class attributes from ActiveRecord objects
        $this->setAttributes();
        $this->parseAttributes();
        // build category nesting sorted array
        $this->expandCategoryNesting();
        // set gallery thumbnail & image if exist
        $this->prepareGallery();
    }

    /**
     * Set class attributes from Content and ContentCategory objects
     * @return void
     */
    private function setAttributes(): void
    {
        $this->id = $this->_content->id;
        $this->title = $this->_content->getLocaled('title');
        $this->text = $this->_content->getLocaled('text');
        $this->display = (bool)$this->_content->display;

        $this->metaTitle = $this->_content->getLocaled('meta_title');
        $this->metaDescription = $this->_content->getLocaled('meta_description');
        $tmpKeywords = $this->_content->getLocaled('meta_keywords');
        $this->metaKeywords = explode(',', $tmpKeywords);

        // set content date, category data
        $this->createDate = Date::humanize($this->_content->created_at);
        $this->catName = $this->_category->getLocaled('title');
        $this->catPath = $this->_category->path;

        // set author user info
        if (App::$User->isExist($this->_content->author_id)) {
            $this->authorId = $this->_content->author_id;
            $this->authorName = Simplify::parseUserNick($this->authorId);
        }

        $this->source = $this->_content->source;
        $this->views = $this->_content->views+1;
        $this->rating = $this->_content->rating;

        // update views count
        $this->_content->views += 1;
        $this->_content->save();
    }

    /**
     * Parse attribute by conditions and apply results
     * @throws ForbiddenException
     */
    private function parseAttributes(): void
    {
        // check if title and text are exists
        if (Str::length($this->title) < 1 || Str::length($this->text) < 1) {
            throw new ForbiddenException('Content of this page is empty!');
        }

        // check if meta title is exist or set default title value
        if (Any::isEmpty($this->metaTitle)) {
            $this->metaTitle = $this->title;
        }

        $ignoredRate = App::$Session->get('content.rate.ignore');
        $this->canRate = true;
        if (Any::isArray($ignoredRate) && Arr::in((string)$this->id, $ignoredRate)) {
            $this->canRate = false;
        }
        if (!App::$User->isAuth()) {
            $this->canRate = false;
        } elseif ($this->authorId === App::$User->identity()->getId()) {
            $this->canRate = false;
        }
    }

    /**
     * Prepare gallery items, poster and thumb
     */
    private function prepareGallery()
    {
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
    }

    /**
     * Expand category nesting array
     * @return void
     */
    private function expandCategoryNesting(): void
    {
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
                        'name' => $record->getLocaled('title'),
                        'path' => $record->path
                    ];
                }
                if (!Str::likeEmpty($catNestingPath)) {
                    $catNestingPath .= '/';
                }
            }
        }

        $this->catNesting[] = [
            'name' => $this->catName,
            'path' => $this->catPath
        ];
    }

    /**
     * Get content record obj
     * @return Content
     */
    public function getRecord()
    {
        return $this->_content;
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
