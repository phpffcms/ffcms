<?php

namespace Apps\Model\Front;

use Ffcms\Core\Arch\Model;

class WallPost extends Model
{
    const MAX_MESSAGE_LENGTH = 500; // 500 symbols

    public $message;

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function setLabels()
    {
        return [
            'message' => null
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validateRules()
    */
    public function setRules()
    {
        return [
            ['message', 'required'],
            ['message', 'length_min', 5],
            ['message', 'length_max', static::MAX_MESSAGE_LENGTH]
        ];
    }

    public function post($fromUser, $toUserWall)
    {

    }


}