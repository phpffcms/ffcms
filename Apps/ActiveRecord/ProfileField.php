<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Serialize;

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

    /**
     * Get all fields using memory cache
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getAll()
    {
        if (MainApp::$Memory->get('custom.fields.all') !== null) {
            return MainApp::$Memory->get('custom.fields.all');
        }

        $records = self::all();
        MainApp::$Memory->set('custom.fields.all', $records);
        return $records;
    }

    /**
     * Get field name locale by field id
     * @param int $id
     * @return array|null|string
     */
    public static function getNameById($id)
    {
        $all = self::getAll();

        $record = $all->find($id);
        if ($record === null || $record === false) {
            return null;
        }

        return Serialize::getDecodeLocale($record->name);
    }

    /**
     * Get field type by field id
     * @param int $id
     * @return array|null|string
     */
    public static function getTypeById($id)
    {
        $all = self::getAll();

        $record = $all->find($id);
        if ($record === null || $record === false) {
            return null;
        }

        return $record->type;
    }

}