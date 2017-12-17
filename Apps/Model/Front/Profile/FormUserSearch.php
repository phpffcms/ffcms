<?php

namespace Apps\Model\Front\Profile;

use Ffcms\Core\Arch\Model;

/**
 * Class FormUserSearch. Search form model business logic
 * @package Apps\Model\Front\Profile
 */
class FormUserSearch extends Model
{
    public $query;

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'query' => __('Nickname')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            ['query', 'required'],
            ['query', 'length_min', 3]
        ];
    }
}