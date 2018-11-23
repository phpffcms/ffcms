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
    public $name;
    public $id;

    /**
     * Widget initialization. Set current pathway to property
     */
    public function init(): void
    {
        if (App::$Request->getLanguage() !== 'en') {
            App::$Translate->append('/i18n/Front/' . App::$Request->getLanguage() . '/CommentWidget.php');
        }
    }

    /**
     * Display comment view
     * @return string
     */
    public function display(): ?string
    {
        if (!$this->name || (int)$this->id < 1) {
            return null;
        }

        return App::$View->render('widgets/comments/show', [
            'configs' => $this->getConfigs(),
            'name' => (string)$this->name,
            'id' => (int)$this->id,
        ]);
    }
}
