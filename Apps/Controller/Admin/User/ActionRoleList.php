<?php

namespace Apps\Controller\Admin\User;

use Apps\ActiveRecord\Role;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait RoleList. List user role groups
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRoleList
{
    /**
     * Show whole role group list
     * @return null|string
     */
    public function listing(): ?string
    {
        return $this->view->render('user/role_list', [
            'records' => Role::all()
        ]);
    }
}
