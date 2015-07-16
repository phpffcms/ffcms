<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\Serialize;

class FormCategoryDelete extends Model
{
    public $title;
    public $path;

    public $moveTo;

    private $_record;

    /**
     * Pass record object
     * @param ContentCategory $record
     */
    public function __construct(ContentCategory $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
    * Pass properties from record construction
    */
    public function before()
    {
        $this->title = Serialize::getDecodeLocale($this->_record->title);
        $this->path = $this->_record->path;
    }

    /**
    * Form labels
    */
    public function labels()
    {
        return [
            'title' => __('Title'),
            'path' => __('Path slug'),
            'moveTo' => __('Move to')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            ['moveTo', 'required'],
            ['moveTo', 'int'],
            ['moveTo', 'in', $this->categoryIds()]
        ];
    }

    /**
     * Make delete category
     * @throws SyntaxException
     * @throws \Exception
     */
    public function make()
    {
        // get new category object
        $newRecord = ContentCategory::find($this->moveTo);
        if ($newRecord === null || $newRecord === false) {
            throw new SyntaxException();
        }

        // get all depended category ids
        $cats = ContentCategory::where('path', 'like', $this->_record->path . '%')->get(['id'])->toArray();
        $allCategoryIds = Arr::ploke('id', $cats);

        // update category_id in content
        $find = Content::whereIn('category_id', $allCategoryIds);
        if ($find->count() > 0) {
            $find->update(['category_id' => $newRecord->id]);
        }

        // remove category
        $this->_record->delete();
    }

    /**
     * List categories
     * @return array
     */
    public function categoryList()
    {
        $result = ContentCategory::getSortedCategories();
        unset($result[$this->_record->id]);
        return $result;
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
            if ($this->_record->id !== $key) {
                $response[] = (string)$key;
            }
        }
        return $response;
    }
}