<?php

namespace Apps\Model\Admin\Content;

use Ffcms\Core\Arch\Model;

class FormContentClear extends Model
{
    public $count = 0;

    public function __construct($count = 0)
    {
        $this->count = $count;
        parent::__construct();
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function labels()
    {
        return [
            'count' => __('Trashed content')
        ];
    }

    /**
    * Typo rules
    */
    public function rules()
    {
        return [];
    }
}