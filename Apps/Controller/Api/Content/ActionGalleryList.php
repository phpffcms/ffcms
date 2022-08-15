<?php

namespace Apps\Controller\Api\Content;

use Apps\ActiveRecord\Content;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\FileSystem\Normalize;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Ffcms\Core\Helper\Date;

/**
 * Trait ActionGalleryList
 * @package Apps\Controller\Api\Content
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionGalleryList
{
    /**
     * Show gallery images from upload directory
     * @param string $id
     * @return string
     * @throws NotFoundException
     * @throws NativeException
     */
    public function galleryList(string $id)
    {
        $this->setJsonHeader();

        // check if user have permission to access there
        if (!App::$User->isAuth() || !App::$User->identity()->role->can('global/file')) {
            throw new NativeException('Permission denied');
        }

        // get news record by id
        $content = Content::find((int)$id);
        if (!$content || $content->id < 1) {
            throw new NativeException(__('Content with id %id% not founded', ['id' => $id]));
        }
        // get content year
        $year = Date::getYear($content->created_at);

        $thumbDir = Normalize::diskFullPath('/upload/gallery/' . $year . '/' . $id . '/orig/');
        if (!Directory::exist($thumbDir)) {
            throw new NotFoundException('Nothing found');
        }

        $files = Directory::scan($thumbDir, null, true);
        if (!$files || !Any::isArray($files) || count($files) < 1) {
            throw new NotFoundException('Nothing found');
        }

        $output = [];
        foreach ($files as $file) {
            $fileExt = Str::lastIn($file, '.');
            $fileName = Str::sub($file, 0, -Str::length($fileExt));
            $output[] = [
                'thumbnailUrl' => '/upload/gallery/' . $year . '/' . $id . '/thumb/' . $fileName . '.jpg',
                'url' => '/upload/gallery/' . $year . '/' . $id . '/orig/' . $file,
                'name' => $file,
                'size' => File::size('/upload/gallery/' . $year . '/' . $id . '/orig/' . $file)
            ];
        }

        return json_encode(['status' => 1, 'files' => $output]);
    }
}
