<?php

namespace Apps\Model\Front\User;

use Apps\ActiveRecord\UserProvider;
use Apps\Model\Front\Profile\FormAvatarUpload;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;
use Symfony\Component\HttpFoundation\File\File as FileObject;

/**
 * Class FormSocialAuth. Model of social authorization and registering on top layer of register model.
 * @package Apps\Model\Front\User
 */
class FormSocialAuth extends FormRegister
{
    /** @var string */
    public $profileLink;

    /** @var string */
    private $_provider_name;
    private $_provider_id;
    /** @var \Hybrid_User_Profile */
    private $_identity;
    /** @var UserProvider */
    private $_record;

    /**
     * FormSocialAuth constructor. Pass provider name as string and identity as object of hybrid auth
     * @param bool $provider
     * @param $identity
     */
    public function __construct($provider, $identity)
    {
        $this->_provider_name = (string)$provider;
        $this->_identity = $identity;
        parent::__construct(false);
    }

    /**
     * Parse user identifier to attributes
     */
    public function before()
    {
        // set unique user id from provider response
        $this->_provider_id = $this->_identity->identifier;

        // grab some data from identity provider
        if ($this->email === null) {
            $this->email = $this->_identity->email;
        }
        $this->profileLink = $this->_identity->profileURL;

        // get record info from db for this identifier if exists
        $this->_record = UserProvider::where('provider_name', '=', $this->_provider_name)
            ->where('provider_id', '=', $this->_provider_id)
            ->first();
    }

    /**
     * Check if this identity always exists
     * @return bool
     */
    public function identityExists()
    {
        return ($this->_record !== null && $this->_record->count() === 1);
    }

    /**
     * Make user authorization from social identity to website session
     * @return bool
     * @throws \Ffcms\Core\Exception\ForbiddenException
     */
    public function makeAuth()
    {
        if ($this->_record === null) {
            return false;
        }
        // get user from belongsTo relation
        $user = $this->_record->user;
        // maybe user was deleted without data provider record?
        if (!$user instanceof iUser) {
            throw new ForbiddenException(__('User related to this social account was deleted'));
        }
        // initialize login model
        $loginModel = new FormLogin();
        // open session & return status
        return $loginModel->openSession($user);
    }

    /**
     * Override default registration function with social auth data compatability
     * @param bool $activation
     * @return bool
     */
    public function tryRegister($activation = false)
    {
        // try to complete register process
        $success = parent::tryRegister($activation);
        if ($success && $this->_userObject !== null) {
            // save remote auth data to relation table
            $provider = new UserProvider();
            $provider->provider_name = $this->_provider_name;
            $provider->provider_id = $this->_provider_id;
            $provider->user_id = $this->_userObject->id;
            $provider->save();

            // get profile object from attr
            $profile = $this->_profileObject;
            // set name from remote service
            if (!Str::likeEmpty($this->_identity->displayName)) {
                $profile->name = $this->_identity->displayName;
            }
            // set profile as user website
            $profile->url = $this->_identity->profileURL;
            // try to get gender (sex)
            if ($this->_identity->gender !== null) {
                $profile->sex = $this->_identity->gender === 'female' ? 2 : 1;
            }
            // set birthday if available
            if ($this->_identity->birthDay !== null && $this->_identity->birthMonth !== null && $this->_identity->birthYear !== null) {
                $profile->birthday = $this->_identity->birthYear . '-' . $this->_identity->birthMonth . '-' . $this->_identity->birthDay;
            }

            // try to parse avatar from remote service
            if ($this->_identity->photoURL !== null) {
                $this->parseAvatar($this->_identity->photoURL, $this->_userObject->id);
            }

            // save profile data
            $profile->save();
        }

        return $success;
    }

    /**
     * Try to download and parse remote avatar
     * @param string $url
     * @param int $userId
     */
    protected function parseAvatar($url, $userId)
    {
        // check if user is defined
        if ((int)$userId < 1) {
            return;
        }

        // check remote image extension
        $imageExtension = Str::lastIn($url, '.', true);
        if (!Arr::in($imageExtension, ['png', 'gif', 'jpg', 'jpeg'])) {
            return;
        }

        // try to get image binary data
        $imageContent = File::getFromUrl($url);
        if ($imageContent === null || Str::likeEmpty($imageContent)) {
            return;
        }

        // write image to filesystem
        $imagePath = '/upload/user/avatar/original/' . $userId . '.' . $imageExtension;
        $write = File::write($imagePath, $imageContent);
        if ($write === false) {
            return;
        }

        // try to write and resize file
        try {
            $fileObject = new FileObject(root . $imagePath);
            $avatarUpload = new FormAvatarUpload();
            $avatarUpload->resizeAndSave($fileObject, $userId, 'small');
            $avatarUpload->resizeAndSave($fileObject, $userId, 'medium');
            $avatarUpload->resizeAndSave($fileObject, $userId, 'big');
        } catch (\Exception $e) {
            if (App::$Debug) {
                App::$Debug->addException($e);
            }
        }
    }
}
