<?php

namespace Widgets\Basic;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Widget;
use Ffcms\Core\Helper\HTML\Listing;

class LanguageSwitcher extends Widget
{
    public function init()
    {
        parent::init();


    }

    public function display()
    {
        parent::display();

        App::$Alias->setCustomLibrary('css', '/vendor/phpffcms/language-flags/flags.css');

        $items = [];
        foreach (App::$Translate->getAvailableLangs() as $lang) {
            $items[] = [
                'type' => 'text',
                'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-'.$lang.'" />',
                'html' => true,
                '!secure' => true
            ];
        }

        return Listing::display([
            'type' => 'ul',
            'property' => ['class' => 'list-inline'],
            'items' => $items
        ]);
    }
}