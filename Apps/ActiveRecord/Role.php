<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\String;

class Role extends ActiveModel
{

    /**
     * Get role object via id
     * @param int $role_id
     * @return object|null
     */
    public function get($role_id)
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
     * @param int|null $user_id
     * @return bool
     */
    public function can($permission, $user_id = null)
    {
        $persone = App::$User->identity($user_id);

        // not founded or not auth
        if ($persone === null || $persone->id < 1) {
            return false;
        }

        $roleObject = $this->get($persone->role_id);
        if ($roleObject === null) {
            return false;
        }

        // no permissions? in any way false
        $permissions = $roleObject->permissions;
        if ($permissions === null || String::length($permissions) < 1) {
            return false;
        }

        // global admin
        if (String::contains('global/all', $permissions)) {
            return true;
        }

        // check if current permission in permissions list for this role
        if (String::contains($permission, $permissions)) {
            return true;
        }

        return false;
    }
}