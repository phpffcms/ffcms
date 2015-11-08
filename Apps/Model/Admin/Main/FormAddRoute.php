<?php

namespace Apps\Model\Admin\Main;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Str;

class FormAddRoute extends Model
{
    public $type;
    public $loader;
    public $source;
    public $target;

    private $cfg;


    /**
    * Load default properties into the model
    */
    public function before()
    {
        $this->cfg = App::$Properties->getAll('Routing');
    }

    /**
    * Define labels for form
    */
    public function labels()
    {
        return [
            'loader' => __('Loader environment'),
            'type' => __('Routing type'),
            'source' => __('Source path/controller'),
            'target' => __('Target path/controller')
        ];
    }

    /**
    * Define validation rules
    */
    public function rules()
    {
        return [
            [['type', 'loader', 'source', 'target'], 'required'],
            ['type', 'in', ['Alias', 'Callback']],
            ['loader', 'in', ['Front', 'Admin', 'Api']],
            [['source', 'target'], 'reverse_match', '/[\'~`\!@#\$%\^&\*\(\)+=\{\}\[\]\|;:"\<\>,\?]/']
        ];
    }

    /**
     * Update config data after add new rule
     */
    public function save()
    {
        $configData = [
            ucfirst(Str::lowerCase($this->type)) => [
                ucfirst(Str::lowerCase($this->loader)) => [
                    '/' . trim($this->source, '/') => '/' . trim($this->target, '/')
                ]
            ]
        ];
        App::$Properties->updateConfig('Routing', $configData);
    }
}