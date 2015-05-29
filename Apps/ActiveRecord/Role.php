<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\String;

class Role extends ActiveModel
{

    /**
     * Get role object via id
     * @param int $role_id
     * @return object|null
     */
    public static function get($role_id)
    {
        $role = App::$Memory->get('user.role.cache.' . $role_id);

        // not founded in cache
        if ($role === null) {
            $role = self::find($role_id);
            App::$Memory->set('user.role.cache.' . $role_id, $role);
        }
        return $role;
    }

    /**
     * Check if user role contains permission
     * @param string $permission
     * @return bool
     */
    public function can($permission)
    {

        // Role::get(id) is not initialized
        if ($this->permissions === null) {
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