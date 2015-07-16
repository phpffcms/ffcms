<?php

namespace Apps\Model\Front\Profile;

use Ffcms\Core\Arch\Model;

class FormUserSearch extends Model
{
    public $query;

    /**
    * Labels
    */
    public function labels()
    {
        return [
            'query' => __('Nickname')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            ['query', 'required'],
            ['query', 'length_min', 3]
        ];
    }
}