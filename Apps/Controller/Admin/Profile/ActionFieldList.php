<?php

namespace Apps\Controller\Admin\Profile;

use Apps\ActiveRecord\ProfileField;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionFieldList
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionFieldList
{
    /**
     * Display profile add_fields list action
     * @return null|string
     */
    public function profileFieldList(): ?string
    {
        return $this->view->render('profile/field_list', [
            'records' => ProfileField::all()
        ]);
    }
}
