<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\UserNotification;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Templex\Url\Url;
use Illuminate\Support\Collection;

/**
 * Class EntityNotificationsList. Build notification messages from active record object
 * @package Apps\Model\Front\Profile
 */
class EntityNotificationsList extends Model
{
    public $items;

    /** @var UserNotification[]|Collection */
    private $_records;

    /**
     * EntityNotificationsList constructor. Pass records object inside.
     * @param Collection|UserNotification[] $records
     */
    public function __construct($records)
    {
        $this->_records = $records;
        parent::__construct();
    }

    /**
     * Build notification list as array
     */
    public function make()
    {
        // check if records is not empty
        if ($this->_records === null) {
            return;
        }

        // list records and build response
        foreach ($this->_records as $record) {
            /** @var UserNotification $record */
            $vars = null;
            if (!Any::isEmpty($record->vars)) {
                $vars = $record->vars;
            }
            if (isset($vars['snippet'])) {
                $vars['snippet'] = Url::a([App::$Alias->baseUrl . $record->uri], $vars['snippet']);
            }

            $text = App::$Translate->get('Profile', $record->msg, $vars);

            $this->items[] = [
                'text' => $text,
                'date' => Date::humanize($record->created_at),
                'new' => !(bool)$record->readed
            ];
        }
    }
}
