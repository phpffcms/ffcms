<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\FileSystem\Normalize;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Gregwar\Image\Image;

class Content extends ApiController
{
    public $maxSize = 512000; // in bytes, 500 * 1024
    public $maxResize = 150;

    public $allowedExt = ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'webp'];

    public function before()
    {
        parent::before();
        $configs = AppRecord::getConfigs('app', 'Content');
        // prevent null-type config data
        if ((int)$configs['gallerySize'] > 0) {
            $this->maxSize = (int)$configs['gallerySize'] * 1024;
        }

        if ((int)$configs['galleryResize'] > 0) {
            $this->maxResize = (int)$configs['galleryResize'];
        }
    }

    /**
     * Upload new files to content item gallery
     * @param $id
     * @return string
     * @throws JsonException
     * @throws NativeException
     * @throws \Exception
     */
    public function actionGalleryupload($id)
    {
        // check if id is passed
        if (Str::likeEmpty($id)) {
            throw new JsonException('Wrong input data');
        }

        // check if user have permission to access there
        if (!App::$User->isAuth() || !App::$User->identity()->getRole()->can('global/file')) {
            throw new NativeException(__('Permissions to upload is denied'));
        }

        // check if directory exist
        if (!Directory::exist('/upload/gallery/' . $id)) {
            Directory::create('/upload/gallery/' . $id);
        }

        // get file object
        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = App::$Request->files->get('gallery-files');
        if ($file === null || $file->getError() !== 0) {
            throw new JsonException(__('Unexpected error in upload process'));
        }

        // check file size
        if ($file->getSize() < 1 || $file->getSize() > $this->maxSize) {
            throw new JsonException(__('File size is too big. Max size: %size%kb', ['size' => intval($this->maxSize/1024)]));
        }

        // check file extension
        if (!Arr::in($file->guessExtension(), $this->allowedExt)) {
            throw new JsonException(__('File extension is not allowed to upload. Allowed: %s%', ['s' => implode(', ', $this->allowedExt)]));
        }

        // create origin directory
        $originPath = '/upload/gallery/' . $id . '/orig/';
        if (!Directory::exist($originPath)) {
            Directory::create($originPath);
        }

        // lets make a new file name
        $fileName = App::$Security->simpleHash($file->getFilename() . $file->getSize());
        $fileNewName = $fileName . '.' . $file->guessExtension();
        // save file from tmp to gallery origin directory
        $file->move(Normalize::diskFullPath($originPath), $fileNewName);

        // lets resize preview image for it
        $thumbPath = '/upload/gallery/' . $id . '/thumb/';
        if (!Directory::exist($thumbPath)) {
            Directory::create($thumbPath);
        }

        $thumb = new Image();
        $thumb->setCacheDir(root . '/Private/Cache/images');

        // open original file, resize it and save
        $thumbSaveName = Normalize::diskFullPath($thumbPath) . '/' . $fileName . '.jpg';
        $thumb->open(Normalize::diskFullPath($originPath) . DIRECTORY_SEPARATOR . $fileNewName)
            ->cropResize($this->maxResize)
            ->save($thumbSaveName, 'jpg', 90);
        $thumb = null;

        $output[] = [
            'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
            'url' => '/upload/gallery/' . $id . '/orig/' . $fileNewName,
            'name' => $fileNewName
        ];

        $this->setJsonHeader();

        // generate success response
        return json_encode(['status' => 1, 'message' => 'ok', 'files' => $output]);
    }

    /**
     * Show gallery images from upload directory
     * @param int $id
     * @return string
     * @throws JsonException
     * @throws NativeException
     */
    public function actionGallerylist($id)
    {
        // check if id is passed
        if (Str::likeEmpty($id)) {
            throw new JsonException('Wrong input data');
        }

        // check if user have permission to access there
        if (!App::$User->isAuth() || !App::$User->identity()->getRole()->can('global/file')) {
            throw new NativeException('Permission denied');
        }

        $thumbDir = Normalize::diskFullPath('/upload/gallery/' . $id . '/orig/');
        if (!Directory::exist($thumbDir)) {
            throw new JsonException('Nothing found');
        }

        $files = Directory::scan($thumbDir, null, true);
        if (!Obj::isArray($files) || count($files) < 1) {
            throw new JsonException('Nothing found');
        }

        $output = [];
        foreach ($files as $file) {
            $fileExt = Str::lastIn($file, '.');
            $fileName = Str::sub($file, 0, -Str::length($fileExt));
            $output[] = [
                'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
                'url' => '/upload/gallery/' . $id . '/orig/' . $file,
                'name' => $file
            ];
        }

        $this->setJsonHeader();
        return json_encode(['files' => $output]);
    }

    public function actionGallerydelete($id, $file)
    {
        // check passed data
        if (Str::likeEmpty($file) || !Obj::isLikeInt($id)) {
            throw new JsonException('Wrong input data');
        }

        // check passed file extension
        $fileExt = Str::lastIn($file, '.', true);
        $fileName = Str::firstIn($file, '.');
        if (!Arr::in($fileExt, $this->allowedExt)) {
            throw new JsonException('Wrong file extension');
        }

        // generate path
        $thumb = '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg';
        $full = '/upload/gallery/' . $id . '/orig/' . $file;

        // check if file exists and remove
        if (File::exist($thumb) || File::exist($full)) {
            File::remove($thumb);
            File::remove($full);
        } else {
            throw new NativeException('Image is not founded');
        }

        return json_encode(['status' => 1, 'msg' => 'Image is removed']);
    }
}