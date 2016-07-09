<?php

namespace Widgets\Front\Comments;

use Ffcms\Core\App;
use Ffcms\Widgets\Ckeditor\Ckeditor;

/**
 * Class Comments. Add, list comments as widget view
 * @package Widgets\Front\Comments
 */
class Comments extends Ckeditor
{
    public $pathway;

    /**
     * Widget initialization. Set current pathway to property
     */
    public function init()
    {
        parent::init();

        if ($this->pathway === null) {
            $this->pathway = App::$Request->getPathInfo();
        }
    }

    /**
     * Display comment view
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function display()
    {
        parent::display();

        $configs = $this->getConfigs();

        return App::$View->render('widgets/comments/show', [
            'configs' => $configs
        ]);
    }


}