<?php

namespace Apps\Model\Front;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;
use Gregwar\Image\Image;

class AvatarUpload extends Model
{
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile */
    public $file;

    const AVATAR_SIZE = 2097152; // 2mb
    const COMPRESS_QUALITY = 90;

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function setLabels()
    {
        return [
            'file' => __('Select avatar')
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validateRules()
    */
    public function setRules()
    {
        return [
            ['file', 'required'],
            ['file', 'isFile', ['jpg', 'png', 'gif', 'jpeg']],
            ['file', 'sizeFile', [1, static::AVATAR_SIZE]]
        ];
    }

    public function inputTypes()
    {
        return [
            'file' => 'file'
        ];
    }

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
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $original
     * @param int $user_id
     * @param string $size
     * @throws \Exception
     * @return null
     */
    protected function resizeAndSave($original, $user_id, $size = 'small')
    {
        $sizeConvert = [
            'big' => [400, 400],
            'medium' => [200, 200],
            'small' => [100, 100]
        ];

        if (!array_key_exists($size, $sizeConvert)) {
            return null;
        }

        Image::open($original->getPathname())
            ->cropResize($sizeConvert[$size][0], $sizeConvert[$size][1])
            ->save(root . '/upload/user/avatar/' . $size . '/' . $user_id . '.jpg', 'jpg', static::COMPRESS_QUALITY);

        return null;
    }
}