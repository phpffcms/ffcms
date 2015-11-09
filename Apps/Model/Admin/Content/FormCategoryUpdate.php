<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;

class FormCategoryUpdate extends Model
{
    public $id;
    public $path;
    public $title = [];
    public $description = [];
    public $configs = [];
    public $dependId = 1;

    private $_pathNested;

    private $_tmpDependId = null;
    private $_record;
    private $_new = false;

    /**
     * Pass record object on construction
     * @param ContentCategory $record
     * @param int|null $dependId
     */
    public function __construct(ContentCategory $record, $dependId = null)
    {
        $this->_record = $record;
        $this->_tmpDependId = $dependId;
        parent::__construct();
    }

    /**
    * Pass properties from default record values
    */
    public function before()
    {
        if ($this->_record->id === null) {
            $this->_new = true;
            // pass owner id category from construct model
            $this->dependId = $this->_tmpDependId < 1 ? 1 : $this->_tmpDependId;
        } else {
            // make tmp id for frontend form
            $this->id = $this->_record->id;
            $path = $this->_record->path;
            // nesting levels
            if (Str::contains('/', $path)) {
                $nestedPath = explode('/', $path);
                $this->path = array_pop($nestedPath);
                $this->_pathNested = implode('/', $nestedPath);
                // get owner category id by nesting path
                $owner = ContentCategory::getByPath($this->_pathNested);
                if ($owner !== null) {
                    $this->dependId = $owner->id;
                }
            } else {
                $this->path = $path;
            }

            // set serialized decoded data
            $this->title = Serialize::decode($this->_record->title);
            $this->description = Serialize::decode($this->_record->description);
            if ($this->_record->configs !== null && !Str::likeEmpty($this->_record->configs)) {
                $this->configs = Serialize::decode($this->_record->configs);
            }
        }
    }

    /**
    * Form labels
    */
    public function labels()
    {
        return [
            'title' => __('Title'),
            'description' => __('Description'),
            'path' => __('Path slug'),
            'dependId' => __('Owner category'),
            'configs.showDate' => __('Show date'),
            'configs.showAuthor' => __('Show author'),
            'configs.showViews' => __('Show views'),
            'configs.showComments' => __('Show comments'),
            'configs.showPoster' => __('Show poster'),
            'configs.showCategory' => __('Show category')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        $rules = [
            [['title', 'description', 'configs'], 'used']
        ];

        // general category
        if ($this->_new === false && (int)$this->_record->id === 1) {
            $rules[] = ['path', 'used'];
        } else {
            $rules[] = ['path', 'required'];
            $rules[] = ['dependId', 'required'];
            $rules[] = ['path', 'Apps\Model\Admin\Content\FormCategoryUpdate::pathIsFree'];
            $rules[] = ['path', 'reverse_match', '/[\/\'~`\!@#\$%\^&\*\(\)+=\{\}\[\]\|;:"\<\>,\?\\\]/'];
        }

        $rules[] = ['title.' . App::$Request->getLanguage(), 'required'];


        return $rules;
    }

    /**
     * Save changed data in db
     */
    public function save()
    {
        $this->_record->title = Serialize::encode(App::$Security->strip_tags($this->title));
        $this->_record->description = Serialize::encode(App::$Security->strip_tags($this->description));
        $savePath = trim($this->_pathNested . '/' . $this->path, '/');
        $this->_record->path = $savePath;
        $this->_record->configs = Serialize::encode($this->configs);
        $this->_record->save();
    }

    /**
     * Get allowed category ids as array (string values for validation)
     * @return array
     */
    public function categoryList()
    {
        $response = ContentCategory::getSortedCategories();
        if ($this->id !== null) {
            unset($response[$this->id]);
        }
        return $response;
    }

    /**
     * Validate pathway
     * @param string $path
     * @return bool
     * @throws SyntaxException
     */
    public function pathIsFree($path)
    {
        $owner = ContentCategory::getById($this->dependId);
        if ($owner === null || $owner === false) {
            throw new SyntaxException();
        }

        // build path with owner category
        $this->_pathNested = $owner->path;
        if (Str::length($this->_pathNested) > 0) {
            $path = $this->_pathNested . '/' . $path;
        }

        // make select for check
        $query = ContentCategory::where('path', '=', $path);
        if ($this->_new !== true) {
            // exclude current category from checking path's
            $query->where('id', '!=', $this->_record->id);
        }

        return $query->count() === 0;
    }
}