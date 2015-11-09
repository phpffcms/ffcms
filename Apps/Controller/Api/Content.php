<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\App;
use Ffcms\Core\Exception\JsonException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\Directory;
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
            throw new NativeException('Permission denied');
        }

        // check if directory exist
        if (!Directory::exist('/upload/gallery/' . $id)) {
            Directory::create('/upload/gallery/' . $id);
        }

        // get file object
        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = App::$Request->files->get('gallery-files');
        if ($file === null || $file->getError() !== 0) {
            throw new JsonException('File not uploaded');
        }

        // check file size
        if ($file->getSize() < 1 || $file->getSize() > $this->maxSize) {
            throw new JsonException('File wrong size');
        }

        // check file extension
        if (!Arr::in($file->guessExtension(), $this->allowedExt)) {
            throw new JsonException('Wrong file extension');
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
        $this->response = json_encode(['files' => $output]);
    }

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
            $fileName = Str::substr($file, 0, -Str::length($fileExt));
            $output[] = [
                'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
                'url' => '/upload/gallery/' . $id . '/orig/' . $file,
                'name' => $file
            ];
        }

        $this->setJsonHeader();
        $this->response = json_encode(['files' => $output]);
    }
}