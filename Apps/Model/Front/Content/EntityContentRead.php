<?php

namespace Apps\Model\Front\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\String;

class EntityContentRead extends Model
{
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

    public $metaTitle;
    public $metaDescription;
    public $metaKeywords;

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
        $this->title = Serialize::getDecodeLocale($this->_content->title);
        $this->text = Serialize::getDecodeLocale($this->_content->text);

        // check if title and text are exists
        if (String::length($this->title) < 1 || String::length($this->text) < 1) {
            throw new ForbiddenException();
        }

        // get meta data
        $this->metaTitle = Serialize::getDecodeLocale($this->_content->meta_title);
        if (String::likeEmpty($this->metaTitle)) {
            $this->metaTitle = $this->title;
        }
        $this->metaDescription = Serialize::getDecodeLocale($this->_content->meta_description);
        $tmpKeywords = Serialize::getDecodeLocale($this->_content->meta_keywords);
        $this->metaKeywords = explode(',', $tmpKeywords);

        $this->createDate = Date::convertToDatetime($this->_content->created_at, Date::FORMAT_TO_HOUR);
        $this->catName = Serialize::getDecodeLocale($this->_category->title);
        $this->catPath = $this->_category->path;
        if (App::$User->isExist($this->_content->author_id)) {
            $this->authorId = $this->_content->author_id;
            $profile = App::$User->identity($this->authorId)->getProfile();
            $this->authorName = String::likeEmpty($profile->nick) ? __('No name') : $profile->nick;
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
                if (!String::likeEmpty($catNestingPath)) {
                    $catNestingPath .= '/';
                }
            }
        }

        $this->catNesting[] = [
            'name' => $this->catName,
            'path' => $this->catPath
        ];

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