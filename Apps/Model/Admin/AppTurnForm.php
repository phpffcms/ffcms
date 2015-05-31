<?php

namespace Apps\Model\Admin;

use Apps\ActiveRecord\App;
use Ffcms\Core\Arch\Model;

class AppTurnForm extends Model
{

    public function updateApp(App $object)
    {
        $status = $object->disabled;

        $object->disabled = (int)!$status; // 0 => 1, 1 => 0
        $object->save();
    }
}