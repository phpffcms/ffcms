<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Helper\Type\Arr;

/**
 * Class Role. Active model for user roles with RBAC permissions.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $name
 * @property string $permissions
 * @property string $color
 * @property string $created_at
 * @property string $updated_at
 */
class Role extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'permissions' => 'string',
        'color' => 'string'
    ];

    /**
     * Get all table data as object
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function all($columns = ['*'])
    {
        $cacheName = 'activerecords.role.all.' . implode('.', $columns);
        $records = MemoryObject::instance()->get($cacheName);
        if ($records === null) {
            $records = parent::all($columns);
            MemoryObject::instance()->set($cacheName, $records);
        }

        return $records;
    }

    /**
     * Get role object via id
     * @param int $roleId
     * @return object|null
     */
    public static function get($roleId)
    {
        $role = MainApp::$Memory->get('user.role.cache.' . $roleId);

        // not founded in cache
        if ($role === null) {
            $role = self::find($roleId);
            MainApp::$Memory->set('user.role.cache.' . $roleId, $role);
        }
        return $role;
    }

    /**
     * Get all roles as array [id=>name]
     * @return null|array
     */
    public static function getIdNameAll(): ?array
    {
        $all = self::all();

        $output = null;
        foreach ($all as $row) {
            $output[$row->id] = $row->name;
        }
        return $output;
    }

    /**
     * Check if user role contains permission
     * @param string $permission
     * @return bool
     */
    public function can($permission): bool
    {
        // Role::get(id) is not initialized
        if (!$this->permissions) {
            return false;
        }

        // global admin
        $permArray = explode(';', $this->permissions);

        if (count($permArray) < 1) {
            return false;
        }

        // admin can all :)
        if (Arr::in('global/all', $permArray)) {
            return true;
        }

        // check if current permission in user permission role
        if (Arr::in($permission, $permArray)) {
            return true;
        }

        return false;
    }
}
