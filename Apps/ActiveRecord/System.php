<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class System. Active record model for 'prefix_systems' table
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $var
 * @property string $data
 */
class System extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'var' => 'string',
        'data' => 'string'
    ];

    /**
     * Get data by variable name
     * @param string $name
     * @return ActiveModel|null|self
     */
    public static function getVar($name)
    {
        return self::where('var', $name)->first();
    }
}
