<?php

namespace Apps\Model\Front\Profile;


use Apps\ActiveRecord\UserNotification;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

/**
 * Class EntityAddNotification. Add user notification in database table
 * @package Apps\Model\Front\Profile
 */
class EntityAddNotification extends Model
{
    const MSG_DEFAULT = 'New notification event: &laquo;%snippet%&raquo;';
    const MSG_ADD_WALLPOST = 'New post on the wall: &laquo;%snippet%&raquo;';
    const MSG_ADD_WALLANSWER = 'New answer &laquo;%snippet%&raquo; for wall post &laquo;%post%&raquo;';
    const MSG_ADD_COMMENTANSWER = 'New answer &laquo;%snippet%&raquo; to your comment &laquo;%post%&raquo;';
    const MSG_ADD_FEEDBACKANSWER = 'New answer &laquo;%snippet%&raquo; to your feedback request &laquo;%post%&raquo;';
    
    private $_targetId;

    /**
     * EntityAddNotification constructor. Pass target user_id inside the model
     * @param bool $targetId
     */
    public function __construct($targetId)
    {
        $this->_targetId = $targetId;
        parent::__construct();
    }

    /**
     * Add notification for user
     * @param string $uri
     * @param string $msg
     * @param array|null $vars
     */
    public function add($uri, $msg = self::MSG_DEFAULT, array $vars = null)
    {
        // save data into database
        $record = new UserNotification();
        $record->user_id = $this->_targetId;
        $record->uri = $uri;
        $record->msg = $msg;
        if ($vars !== null) {
            $record->vars = Serialize::encode($vars);
        }

        $record->save();
    }
}