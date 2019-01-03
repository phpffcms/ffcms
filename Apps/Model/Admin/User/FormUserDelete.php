<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\User;
use Apps\ActiveRecord\WallPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;

/**
 * Class FormUserDelete. Delete users as passed array of them ids.
 * @package Apps\Model\Admin\User
 */
class FormUserDelete extends Model
{
    /** @var User[] */
    public $users;
    public $delete = false;

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
            /** @var User $user */
            $user = App::$User->identity($id);
            if ($user) {
                $this->users[] = $user;
            }
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'email' => __('Email'),
            'login' => __('Login'),
            'delete' => __('Delete user content')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            ['delete', 'required'],
            ['delete', 'boolean']
        ];
    }

    /**
     * Delete user from database
     * @throws \Exception
     */
    public function delete()
    {
        foreach ($this->users as $user) {
            $uid = $user->id;
            // delete whole website info for this user
            if ((bool)$this->delete) {
                $model = new FormUserClear($user);
                $model->comments = true;
                $model->content = true;
                $model->feedback = true;
                $model->wall = true;
                $model->make();
            }

            // delete avatars
            File::remove('/upload/user/avatar/big/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/medium/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/small/' . $uid . '.jpg');
            File::remove('/upload/user/avatar/original/' . $uid . '.jpg');
            // delete user profile and auth data
            $user->profile()->delete();
            // delete user provider data
            $user->provider()->delete();
            // delete user object
            $user->delete();
        }
    }
}
