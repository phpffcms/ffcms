<?php

namespace Apps\Model\Front\Profile;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;
use Gregwar\Image\Image;

/**
 * Class FormAvatarUpload. Upload user avatar from settings form
 * @package Apps\Model\Front\Profile
 */
class FormAvatarUpload extends Model
{
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile */
    public $file;

    const AVATAR_SIZE = 600; // 2mb
    const COMPRESS_QUALITY = 90;

    /**
     * Form text helper data with translation
     * @return array
     */
    public function labels(): array
    {
        return [
            'file' => __('Select photo')
        ];
    }

    /**
     * Validation rules for avatar uploading
     * @return array
     */
    public function rules(): array
    {
        return [
            ['file', 'required'],
            ['file', 'isFile', ['jpg', 'png', 'gif', 'jpeg']],
            ['file', 'sizeFile', [1, static::AVATAR_SIZE]]
        ];
    }

    /**
     * Input data sources
     * @return array
     */
    public function sources(): array
    {
        return [
            'file' => 'file'
        ];
    }

    /**
     * Make 3 copy from original user avatar with compression: small, medium and big
     * @param iUser $user
     */
    public function copyFile(iUser $user)
    {
        // move file to original folder
        $upload = $this->file->move(root . '/upload/user/avatar/original/', $user->id . '.' . $this->file->guessExtension());

        try {
            // big image
            $this->resizeAndSave($upload, $user->id, 'big');
            $this->resizeAndSave($upload, $user->id, 'medium');
            $this->resizeAndSave($upload, $user->id, 'small');
        } catch (\Exception $e) {
            if (App::$Debug) {
                App::$Debug->addException($e);
            }
        }
    }

    /**
     * Resize user avatar from uploaded picture
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|\Symfony\Component\HttpFoundation\File\File $original
     * @param int $user_id
     * @param string $size
     * @throws \Exception
     * @return null
     */
    public function resizeAndSave($original, $user_id, $size = 'small')
    {
        $sizeConvert = [
            'big' => [400, 400],
            'medium' => [200, 200],
            'small' => [100, 100]
        ];

        if (!array_key_exists($size, $sizeConvert)) {
            return null;
        }

        $image = new Image();
        $image->setCacheDir(root . '/Private/Cache/images');

        $image->open($original->getPathname())
            ->cropResize($sizeConvert[$size][0], $sizeConvert[$size][1])
            ->save(root . '/upload/user/avatar/' . $size . '/' . $user_id . '.jpg', 'jpg', static::COMPRESS_QUALITY);

        return null;
    }
}
