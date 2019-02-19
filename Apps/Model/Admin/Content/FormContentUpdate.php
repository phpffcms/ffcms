<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Apps\ActiveRecord\ContentTag;
use Apps\ActiveRecord\User;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Integer;
use Ffcms\Core\Helper\Type\Str;
use Illuminate\Support\Collection;

/**
 * Class FormContentUpdate. Create and update content items business model
 * @package Apps\Model\Admin\Content
 */
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
    public $display = 1;
    public $source;
    public $addRating = 0;
    public $createdAt;
    public $important;
    public $tpl;

    public $galleryFreeId;

    private $_content;
    private $cloneId;
    private $_new = false;

    /**
     * FormContentUpdate constructor. Pass content active record inside
     * @param Content|Collection $content
     * @param int $cloneId
     */
    public function __construct(Content $content, int $cloneId = 0)
    {
        $this->_content = $content;
        $this->cloneId = $cloneId;
        parent::__construct();
    }

    /**
     * Set model properties from active record data
     */
    public function before()
    {
        // is new item?
        if (!$this->_content->id) {
            $this->_new = true;
            if (!$this->galleryFreeId) {
                $this->galleryFreeId = '_tmp_' . Str::randomLatin(mt_rand(16, 32));
            }

            if (!$this->authorId) {
                $this->authorId = App::$User->identity()->getId();
            }

            if (!$this->categoryId) {
                $this->categoryId = 1;
            }
            if (!$this->path) {
                $this->path = Integer::random(8) . '-' . date('d-m-Y');
            }

            if ($this->cloneId > 0) {
                $template = Content::find($this->cloneId);
                if ($template) {
                    $this->title = $template->title;
                    $this->text = $template->text;
                    $this->metaTitle = $template->meta_title;
                    $this->metaDescription = $template->meta_description;
                    $this->metaKeywords = $template->meta_keywords;
                }
            }
        } else { // is edit of exist item? define available data
            $this->title = $this->_content->title;
            $this->text = $this->_content->text;
            $this->path = $this->_content->path;
            $this->poster = $this->_content->poster;
            $this->categoryId = $this->_content->category_id;
            $this->authorId = $this->_content->author_id;
            $this->metaTitle = $this->_content->meta_title;
            $this->metaKeywords = $this->_content->meta_keywords;
            $this->metaDescription = $this->_content->meta_description;
            $this->display = $this->_content->display;
            $this->source = $this->_content->source;
            $this->createdAt = Date::convertToDatetime($this->_content->created_at, Date::FORMAT_TO_HOUR);
            $this->galleryFreeId = $this->_content->id;
            $this->important = $this->_content->important;
            $this->tpl = $this->_content->tpl;
        }
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        $res = [
            ['title.' . App::$Request->getLanguage(), 'required'],
            ['text.' . App::$Request->getLanguage(), 'required'],
            ['text', 'used'],
            ['path', 'reverse_match', '/[\/\'~`\!@#\$%\^&\*\(\)+=\{\}\[\]\|;:"\<\>,\?\\\]/'],
            [['path', 'categoryId', 'authorId', 'display', 'galleryFreeId', 'title', 'important', 'tpl'], 'required'],
            [['metaTitle', 'metaKeywords', 'metaDescription', 'poster', 'source', 'addRating', 'createdAt'], 'used'],
            [['addRating', 'authorId', 'display'], 'int'],
            [['important', 'display'], 'boolean'],
            ['categoryId', 'in', $this->categoryIds()],
            ['path', '\Apps\Model\Admin\Content\FormContentUpdate::validatePath'],
            ['tpl', '\Apps\Model\Admin\Content\FormContentUpdate::validateTemplate'],
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
     * Filtering attribute types
     * @return array
     */
    public function types(): array
    {
        return [
            'text' => '!secure'
        ];
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
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
            'important' => __('Make important'),
            'createdAt' => __('Publish date'),
            'authorId' => __('Author identity'),
            'source' => __('Source URL'),
            'addRating' => __('Change rating'),
            'poster' => __('Poster'),
            'tpl' => __('Template')
        ];
    }

    /**
     * Save changes in database
     */
    public function save()
    {
        $this->_content->title = $this->title;
        $this->_content->text = $this->text;
        $this->_content->path = $this->path;
        $this->_content->category_id = $this->categoryId;
        $this->_content->author_id = $this->authorId;
        $this->_content->display = $this->display;
        $this->_content->meta_title = $this->metaTitle;
        $this->_content->meta_keywords = $this->metaKeywords;
        $this->_content->meta_description = $this->metaDescription;
        $this->_content->source = $this->source;
        $this->_content->important = (int)$this->important;
        $this->_content->tpl = $this->tpl;
        // check if rating is changed
        if ((int)$this->addRating !== 0) {
            $this->_content->rating += (int)$this->addRating;
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
        
        // update tags data in special table (relation: content->content_tags = oneToMany)
        ContentTag::where('content_id', $this->_content->id)->delete();
        $insertData = [];
        foreach ($this->metaKeywords as $lang => $keys) {
            // split keywords to tag array
            $tags = explode(',', $keys);
            foreach ($tags as $tag) {
                // cleanup tag from white spaces
                $tag = trim($tag);
                // prepare data to insert
                if (Str::length($tag) > 0) {
                    $insertData[] = [
                        'content_id' => $this->_content->id,
                        'lang' => $lang,
                        'tag' => $tag
                    ];
                }
            }
        }
        // insert tags
        ContentTag::insert($insertData);

        // move files
        if ($tmpGalleryId !== $this->_content->id) {
            Directory::rename('/upload/gallery/' . $tmpGalleryId, $this->_content->id);
        }
    }

    /**
     * Get allowed category ids as array
     * @return array
     */
    public function categoryIds(): ?array
    {
        $data = ContentCategory::getSortedCategories();
        return array_keys($data);
    }

    /**
     * Validate path filter
     * @return bool
     */
    public function validatePath(): bool
    {
        // try to find this item
        $find = Content::where('path', $this->path);
        // exclude self id
        if ($this->_content->id && Any::isInt($this->_content->id)) {
            $find->where('id', '!=', $this->_content->id);
        }

        // limit only current category id
        $find->where('category_id', $this->categoryId);
        return $find->count() < 1;
    }

    /**
     * Check if template is exist
     * @return bool
     */
    public function validateTemplate(): bool
    {
        return Arr::in($this->tpl, $this->getAvailableTemplates());
    }

    /**
     * Get users id->nick+mail list
     * @return array|null
     */
    public function getUserIdName(): ?array
    {
        $users = [];
        User::with('profile')->get()->each(function ($user) use (&$users) {
            /** @var User $user */
            $users[$user->id] = ($user->profile->nick ?? 'id' . $user->id) . ' (' . $user->email . ')';
        });

        return $users;
    }

    /**
     * Check if news is new
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->_new;
    }

    /**
     * Get content comments
     * @return CommentPost[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public function getComments()
    {
        if ($this->isNew()) {
            return null;
        }

        return $this->_content->commentPosts;
    }

    /**
     * Get available templates for content item in front
     * @return array
     */
    public function getAvailableTemplates(): array
    {
        $theme = App::$Properties->get('theme')['Front'] ?? 'default';
        $dir = File::listFiles('/Apps/View/Front/' . $theme . '/content/tpl', ['.php'], true);
        if (!$dir || count($dir) < 1) {
            $dir = ['default'];
        }

        foreach ($dir as $idx => $file) {
            $dir[$idx] = Str::sub($file, 0, strlen($file)-4);
        }

        return $dir;
    }
}
