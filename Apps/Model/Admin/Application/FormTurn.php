<?php

namespace Apps\Model\Admin\Application;

use Apps\ActiveRecord\App;
use Ffcms\Core\Arch\Model;

class FormTurn extends Model
{

    public function update(App $object)
    {
        $status = $object->disabled;

        $object->disabled = (int)!$status; // magic inside: bool to int and reverse - 0 => 1, 1 => 0
        $object->save();
    }
}