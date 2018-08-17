<?php

namespace Widgets\Front\Comments;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Widget;

/**
 * Class Comments. Add, list comments as widget view
 * @package Widgets\Front\Comments
 */
class Comments extends Widget
{
    public $pathway;

    /**
     * Widget initialization. Set current pathway to property
     */
    public function init(): void
    {
        if (App::$Request->getLanguage() !== 'en') {
            App::$Translate->append('/i18n/Front/' . App::$Request->getLanguage() . '/CommentWidget.php');
        }

        if (!$this->pathway) {
            $this->pathway = App::$Request->getPathInfo();
        }
    }

    /**
     * Display comment view
     * @return string
     */
    public function display(): ?string
    {
        return App::$View->render('widgets/comments/show', [
            'configs' => $this->getConfigs()
        ]);
    }
}
