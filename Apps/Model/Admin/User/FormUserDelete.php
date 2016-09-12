<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\WallPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormUserDelete. Delete users as passed array of them ids.
 * @package Apps\Model\Admin\User
 */
class FormUserDelete extends Model
{
    public $users;

    private $_ids;

    /**
     * FormUserDelete constructor. Pass user ids inside
     * @param array $ids
     */
    public function __construct(array $ids)
    {
        $this->_ids = $ids;
        parent::__construct(true);
    }

    /**
     * Set user data to model property
     */
    public function before()
    {
        // try to find each user
        foreach ($this->_ids as $id) {
            $user = App::$User->identity($id);
            if ($user !== null) {
                $this->users[] = $user;
            }
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels()
    {
        return [
            'email' => __('Email'),
            'login' => __('Login')
        ];
    }

    /**
     * Delete user from database
     * @throws \Exception
     */
    public function delete()
    {
        foreach ($this->users as $user) {
            /** @var iUser $user */
            $uid = $user->getParam('id');
            // delete wall records
            WallPost::where('target_id', '=', $uid)
                ->orWhere('sender_id', '=', $uid)
                ->delete();
            // delete avatars
            File::remove('/upload/user/avatar/big/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/medium/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/small/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/original/' . $uid . '.jpg');
            // delete user profile and auth data
            $user->getProfile()->delete();
            // delete user provider data
            $user->getProviders()->delete();
            // delete user object
            $user->delete();
        }
    }


}