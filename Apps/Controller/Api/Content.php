<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\App as AppRecord;
use Apps\ActiveRecord\Content as ContentRecord;
use Apps\Model\Api\Content\ContentRatingChange;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
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

    /**
     * Prepare configuratins before initialization
     */
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
     * Change content item rating action
     * @param string $type
     * @param int $id
     * @throws NativeException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @return string
     */
    public function actionChangerate($type, $id)
    {
        // check input params
        if (!Arr::in($type, ['plus', 'minus']) || !Obj::isLikeInt($id)) {
            throw new NativeException('Bad conditions');
        }
        
        // get current user and check is authed
        $user = App::$User->identity();
        if ($user === null || !App::$User->isAuth()) {
            throw new ForbiddenException(__('Authorization is required!'));
        }

        // set ignored content id to rate in session
        $ignored = App::$Session->get('content.rate.ignore');
        $ignored[] = $id;
        App::$Session->set('content.rate.ignore', $ignored);
        
        // find content record
        $record = ContentRecord::find($id);
        if ($record === null || $record->count() < 1) {
            throw new NotFoundException(__('Content item is not founded'));
        }

        // check if author rate him-self content
        if ($record->author_id === $user->getId()) {
            throw new ForbiddenException(__('You can not rate your own content'));
        }
        
        // initialize model
        $model = new ContentRatingChange($record, $type, $user);
        // check if content items is already rated by this user
        if ($model->isAlreadyRated()) {;
            throw new ForbiddenException(__('You have already rate this!'));            
        }
        
        // make rate - add +1 to content rating and author rating
        $model->make();
        
        return json_encode([
            'status' => 1,
            'rating' => $model->getRating()
        ]);
    }

    /**
     * Upload new files to content item gallery
     * @param int $id
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws \Exception
     */
    public function actionGalleryupload($id)
    {
        // check if id is passed
        if (Str::likeEmpty($id)) {
            throw new NativeException('Wrong input data');
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

        $this->setJsonHeader();
        return json_encode(['status' => 1, 'file' => [
            'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
            'url' => '/upload/gallery/' . $id . '/orig/' . $fileNewName,
            'name' => $fileNewName
        ]]);
    }

    /**
     * Show gallery images from upload directory
     * @param int $id
     * @return string
     * @throws NotFoundException
     * @throws NativeException
     */
    public function actionGallerylist($id)
    {
        // check if id is passed
        if (Str::likeEmpty($id)) {
            throw new NativeException('Wrong input data');
        }

        // check if user have permission to access there
        if (!App::$User->isAuth() || !App::$User->identity()->getRole()->can('global/file')) {
            throw new NativeException('Permission denied');
        }

        $thumbDir = Normalize::diskFullPath('/upload/gallery/' . $id . '/orig/');
        if (!Directory::exist($thumbDir)) {
            throw new NotFoundException('Nothing found');
        }

        $files = Directory::scan($thumbDir, null, true);
        if ($files === false || !Obj::isArray($files) || count($files) < 1) {
            throw new NotFoundException('Nothing found');
        }

        $output = [];
        foreach ($files as $file) {
            $fileExt = Str::lastIn($file, '.');
            $fileName = Str::sub($file, 0, -Str::length($fileExt));
            $output[] = [
                'thumbnailUrl' => '/upload/gallery/' . $id . '/thumb/' . $fileName . '.jpg',
                'url' => '/upload/gallery/' . $id . '/orig/' . $file,
                'name' => $file,
                'size' => File::size('/upload/gallery/' . $id . '/orig/' . $file)
            ];
        }

        $this->setJsonHeader();
        return json_encode(['status' => 1, 'files' => $output]);
    }

    /**
     * Remove items from gallery (preview+full)
     * @param int $id
     * @param string $file
     * @throws ForbiddenException
     * @throws NativeException
     * @return string
     */
    public function actionGallerydelete($id, $file = null)
    {
        if ($file === null || Str::likeEmpty($file)) {
            $file = (string)$this->request->query->get('file', null);
        }
        // check passed data
        if (Str::likeEmpty($file) || !Obj::isLikeInt($id)) {
            throw new NativeException('Wrong input data');
        }

        // check passed file extension
        $fileExt = Str::lastIn($file, '.', true);
        $fileName = Str::firstIn($file, '.');
        if (!Arr::in($fileExt, $this->allowedExt)) {
            throw new ForbiddenException('Wrong file extension');
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