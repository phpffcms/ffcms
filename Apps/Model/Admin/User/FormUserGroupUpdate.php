<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\Role;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FormUserGroupUpdate. Business logic of user group update in database.
 * @package Apps\Model\Admin\User
 */
class FormUserGroupUpdate extends Model
{
    public $name;
    public $permissions;
    public $color;

    private $_role;

    /**
     * FormUserGroupUpdate constructor. Pass role object inside.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->_role = $role;
        parent::__construct(true);
    }

    /**
     * Parse public attribute values from input object data
     */
    public function before()
    {
        $this->name = $this->_role->name;
        $this->color = $this->_role->color;
        if ($this->_role->permissions !== null) {
            $this->permissions = explode(';', $this->_role->permissions);
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels()
    {
        return [
            'name' => __('Name'),
            'permissions' => __('Permissions'),
            'color' => __('Group color')
        ];
    }

    /**
     * Form validation rules
     * @return array
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'length_min', 3],
            ['permissions', 'used'],
            ['color', 'used'],
            ['color', 'length_max', 7]
        ];
    }

    /**
     * Get all permissions as array
     * @return array
     * @throws SyntaxException
     */
    public function getAllPermissions()
    {
        $p = App::$Properties->getAll('permissions');
        if ($p === false || !Obj::isArray($p)) {
            throw new SyntaxException('User permissions settings is not founded: /Private/Config/Permissions.php');
        }

        return $p;
    }

    /**
     * Save new user group data in database after submit
     */
    public function save()
    {
        $this->_role->name = $this->name;
        $this->_role->color = $this->color;
        if (!Obj::isArray($this->permissions) || count($this->permissions) < 1) {
            $this->_role->permissions = '';
        } else {
            $this->_role->permissions = implode(';', $this->permissions);
        }
        $this->_role->save();
    }
}