<?php

namespace Apps\Controller\Api\Content;

use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\FileSystem\Normalize;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Gregwar\Image\Image;

/**
 * Trait ActionGalleryUpload
 * @package Apps\Controller\Api\Content
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionGalleryUpload
{
    /**
     * Upload new files to content item gallery
     * @param string $id
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws \Exception
     */
    public function galleryUpload(string $id)
    {
        $this->setJsonHeader();

        // check if id is passed
        if (Str::likeEmpty($id)) {
            throw new NativeException('Wrong input data');
        }

        // check if user have permission to access there
        if (!App::$User->isAuth() || !App::$User->identity()->role->can('global/file')) {
            throw new NativeException(__('Permissions to upload is denied'));
        }

        // check if directory exist
        if (!Directory::exist('/upload/gallery/' . $id)) {
            Directory::create('/upload/gallery/' . $id);
        }

        // get file object
        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = $this->request->files->get('file');
        if ($file === null || $file->getError() !== 0) {
            throw new NativeException(__('Unexpected error in upload process'));
        }

        // check file size
        if ($file->getSize() < 1 || $file->getSize() > $this->maxSize) {
            throw new ForbiddenException(__('File size is too big. Max size: %size%kb', ['size' => (int)($this->maxSize/1024)]));
        }

        // check file extension
        if (!Arr::in($file->guessExtension(), $this->allowedExt)) {
            throw new ForbiddenException(__('File extension is not allowed to upload. Allowed: %s%', ['s' => implode(', ', $this->allowedExt)]));
        }

        // create origin directory
        $originPath = '/upload/gallery/' . $id . '/orig/';
        if (!Directory::exist($originPath)) {
            Directory::create($originPath);
        }

        // lets make a new file name
        $fileName = App::$Security->simpleHash($file->getClientOriginalName() . $file->getSize());
        $fileNewName = $fileName . '.' . $file->guessExtension();
        // check if image is already loaded
        if (File::exist($originPath . $fileNewName)) {
            throw new ForbiddenException(__('File is always exists!'));
        }
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

        return json_encode(['status' => 1, 'file' => [
            'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
            'url' => '/upload/gallery/' . $id . '/orig/' . $fileNewName,
            'name' => $fileNewName
        ]]);
    }
}
