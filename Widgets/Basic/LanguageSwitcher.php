<?php

namespace Widgets\Basic;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Widget;
use Ffcms\Core\Helper\HTML\Listing;

class LanguageSwitcher extends Widget
{
    private $multiLangEnabled = false;

    public $css = ['class' => 'list-inline'];
    public $onlyArrayItems = false;

    public function init()
    {
        $this->multiLangEnabled = App::$Properties->get('multiLanguage', 'default', true);

        if ($this->multiLangEnabled === true) {
            App::$Alias->setCustomLibrary('css', '/vendor/phpffcms/language-flags/flags.css');
        }
    }

    public function display()
    {
        // prevent loading on disabled multi-language property
        if ($this->multiLangEnabled !== true) {
            return null;
        }

        $items = [];
        foreach (App::$Translate->getAvailableLangs() as $lang) {
            $items[] = [
                'type' => 'link',
                'link' => App::$Alias->baseUrlNoLang . '/' . $lang . App::$Request->getPathInfo(),
                'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-'.$lang.'" />',
                'html' => true,
                '!secure' => true
            ];
        }

        if ($this->onlyArrayItems) {
            return $items;
        }

        return Listing::display([
            'type' => 'ul',
            'property' => $this->css,
            'items' => $items
        ]);
    }
}