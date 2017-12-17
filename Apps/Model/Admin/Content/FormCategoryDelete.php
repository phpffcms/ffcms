<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Arr;

/**
 * Class FormCategoryDelete. Delete category business logic model
 * @package Apps\Model\Admin\Content
 */
class FormCategoryDelete extends Model
{
    public $title;
    public $path;

    public $moveTo;

    private $_record;

    /**
     * ForumCategoryDelete constructor. Pass active record object inside
     * @param ContentCategory $record
     */
    public function __construct(ContentCategory $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
     * Set model public attributes from record object
     */
    public function before()
    {
        $this->title = $this->_record->getLocaled('title');
        $this->path = $this->_record->path;
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'title' => __('Title'),
            'path' => __('Path slug'),
            'moveTo' => __('Move to')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
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
        $allCategoryIds = Arr::pluck('id', $cats);

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
     * Get allowed category ids as array
     * @return array
     */
    public function categoryIds()
    {
        $data = ContentCategory::getSortedCategories();
        $response = array_keys($data);
        // remove current category id from 'moveTo" list
        foreach ($response as $k => $v) {
            if ($this->_record->id === $v) {
                unset($response[$k]);
            }
        }
        return $response;
    }
}