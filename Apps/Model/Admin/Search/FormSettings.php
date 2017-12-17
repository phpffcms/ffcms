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

    private $_configs;

    /**
     * ForumSettings constructor. Construct model with default values
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    public function before()
    {
        if ($this->_configs === null) {
            return;
        }
        foreach ($this->_configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Labels for admin settings form
     * @return array
     */
    public function labels(): array
    {
        return [
            'itemPerApp' => __('Search count'),
            'minLength' => __('Min length')
        ];
    }

    /**
     * Validation rules
     * @return @array
     */
    public function rules(): array
    {
        return [
            [['itemPerApp', 'minLength'], 'required'],
            [['itemPerApp', 'minLength'], 'int']
        ];
    }
}