<?php

namespace Widgets\Front\Comments;

use Ffcms\Core\App;
use Ffcms\Widgets\Ckeditor\Ckeditor;

class Comments extends Ckeditor
{
    public $pathway;

    public function init()
    {
        parent::init();

        if ($this->pathway === null) {
            $this->pathway = App::$Request->getPathInfo();
        }
    }

    public function display()
    {
        parent::display();

        $configs = $this->getConfigs();

        return App::$View->render('widgets/comments/show', [
            'configs' => $configs
        ]);
    }


}