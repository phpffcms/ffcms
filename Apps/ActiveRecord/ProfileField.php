<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;

/**
 * Class ProfileField. Active record model for additional profile fields management
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $reg_exp
 * @property string $reg_cond
 * @property string $created_at
 * @property string $updated_at
 */
class ProfileField extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'type' => 'string',
        'name' => 'serialize',
        'reg_exp' => 'string',
        'reg_cond' => 'string'
    ];

    /**
     * Get all table data using memory cache
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function all($columns = ['*'])
    {
        $cacheName = 'activerecord.profilefield.all.' . implode('.', $columns);
        $records = MemoryObject::instance()->get($cacheName);
        if ($records === null) {
            $records = parent::all($columns);
            MemoryObject::instance()->set($cacheName, $records);
        }

        return $records;
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getAll()
    {
        return self::all();
    }

    /**
     * Get field name locale by field id
     * @param int $id
     * @return array|null|string
     */
    public static function getNameById($id)
    {
        $all = self::all();

        $record = $all->find($id);
        if ($record === null || $record === false) {
            return null;
        }

        return $record->getLocaled('name');
    }

    /**
     * Get field type by field id
     * @param int $id
     * @return array|null|string
     */
    public static function getTypeById($id)
    {
        $all = self::all();

        $record = $all->find($id);
        if ($record === null || $record === false) {
            return null;
        }

        return $record->type;
    }

}