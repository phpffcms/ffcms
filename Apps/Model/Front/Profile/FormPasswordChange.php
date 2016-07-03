<?php

namespace Apps\Model\Front\Profile;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormPasswordChange. Business logic model for password change form.
 * @package Apps\Model\Front\Profile
 */
class FormPasswordChange extends Model
{
    public $current;
    public $new;
    public $renew;

    private $_user;

    /**
     * Constructor with user object relation
     * @param iUser $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct(true);
    }

    /**
     * Labels
     * @return array
     */
    public function labels()
    {
        return [
            'current' => __('Current password'),
            'new' => __('New password'),
            'renew' => __('Repeat password')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules()
    {
        return [
            [['current', 'new', 'renew'], 'required'],
            ['new', 'length_min', 4],
            ['current', 'Apps\Model\Front\Profile\FormPasswordChange::passwordCheck'],
            ['new', 'equal', $this->getRequest('renew', $this->getSubmitMethod())]
        ];
    }

    /**
     * Set new password after validation passing
     */
    public function make()
    {
        $crypt = App::$Security->password_hash($this->new);
        $this->_user->password = $crypt;
        $this->_user->save();
    }

    /**
     * Check current user password comparing hashes
     * @param string $object
     * @return bool
     */
    public function passwordCheck($object)
    {
        return App::$Security->password_hash($object) === $this->_user->password;
    }
}