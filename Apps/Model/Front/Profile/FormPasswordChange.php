<?php

namespace Apps\Model\Front\Profile;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Crypt;
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
     * Form display labels
     * @return array
     */
    public function labels(): array
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
    public function rules(): array
    {
        return [
            [['current', 'new', 'renew'], 'required'],
            ['new', 'length_min', 6],
            ['new', 'passwordStrong'],
            ['current', 'Apps\Model\Front\Profile\FormPasswordChange::passwordCheck'],
            ['new', 'equal', $this->getRequest('renew', $this->getSubmitMethod())]
        ];
    }

    /**
     * Set new password after validation passing
     */
    public function make()
    {
        $this->_user->password = Crypt::passwordHash($this->new);
        $this->_user->save();
    }

    /**
     * Check current user password comparing hashes
     * @param string $object
     * @return bool
     */
    public function passwordCheck($object)
    {
        return Crypt::passwordVerify($object, $this->_user->password);
    }
}
