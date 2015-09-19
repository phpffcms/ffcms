<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\Role;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Type\Object;

class FormUserGroupUpdate extends Model
{
    public $name;
    public $permissions;

    private $_role;

    /**
     * Initialize model
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->_role = $role;
        parent::__construct();
    }

    /**
    * Magic method before example
    */
    public function before()
    {
        $this->name = $this->_role->name;
        if ($this->_role->permissions !== null) {
            $this->permissions = explode(';', $this->_role->permissions);
        }
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function labels()
    {
        return [
            'name' => __('Name'),
            'permissions' => __('Permissions')
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validate()
    */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'length_min', 3],
            ['permissions', 'used']
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
        if ($p === false || !Object::isArray($p)) {
            throw new SyntaxException('User permissions settings is not founded: /Private/Config/Permissions.php');
        }

        return $p;
    }

    public function save()
    {
        $this->_role->name = $this->name;
        $this->_role->permissions = implode(';', $this->permissions);
        $this->_role->save();
    }
}