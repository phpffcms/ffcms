<?php

namespace Widgets\Basic;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Widget;
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Type\Obj;

/**
 * Class LanguageSwitcher. Show language switched as listing html object
 * @package Widgets\Basic
 */
class LanguageSwitcher extends Widget
{
    private $multiLangEnabled = false;
    private $langs = [];

    public $css = ['class' => 'list-inline'];
    public $onlyArrayItems = false;

    /**
     * Set configurations values in widget attributes
     */
    public function init()
    {
        $this->multiLangEnabled = App::$Properties->get('multiLanguage', 'default', true);
        $this->langs = App::$Properties->get('languages', 'default', []);

        if ($this->multiLangEnabled === true) {
            App::$Alias->setCustomLibrary('css', '/vendor/phpffcms/language-flags/flags.css');
        }
    }

    /**
     * Display language switcher as html or get builded result as array
     * @return array|null|string
     */
    public function display()
    {
        // prevent loading on disabled multi-language property
        if ($this->multiLangEnabled !== true) {
            return null;
        }

        // check if languages is defined and count more then 1
        if (!Obj::isArray($this->langs) || count($this->langs) < 2) {
            return null;
        }

        // build output items for listing
        $items = [];
        foreach ($this->langs as $lang) {
            $items[] = [
                'type' => 'link',
                'link' => App::$Alias->baseUrlNoLang . '/' . $lang . App::$Request->getPathInfo(),
                'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-'.$lang.'" alt="'.$lang.'"/>',
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