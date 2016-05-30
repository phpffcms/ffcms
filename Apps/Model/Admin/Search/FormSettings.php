<?php

namespace Apps\Model\Admin\Search;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Model to transfer and validate input data & attributes for search admin configs.
 * @package Apps\Model\Admin\Search
 */
class FormSettings extends Model
{
    public $itemPerApp;
    public $minLength;

    /**
     * Construct model with default values
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        parent::__construct();
    }

    /**
    * Labels for admin settings form
    */
    public function labels()
    {
        return [
            'itemPerApp' => __('Search count'),
            'minLength' => __('Min length')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            [['itemPerApp', 'minLength'], 'required'],
            [['itemPerApp', 'minLength'], 'int']
        ];
    }
}