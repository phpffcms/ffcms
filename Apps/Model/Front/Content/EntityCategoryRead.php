<?php

namespace Apps\Model\Front\Content;

use Ffcms\Core\Arch\Model;

class EntityCategoryRead extends Model
{
    public $test;

    /**
    * Magic method before example
    */
    public function before()
    {
        $this->test = 'Example of usage class property';
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function labels()
    {
        return [
            'test' => 'Label for test'
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validate()
    */
    public function rules()
    {
        return [
            ['test', 'required']
        ];
    }
}