<?php

namespace Apps\Controller\Api\Content;

use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionGalleryDelete
 * @package Apps\Controller\Api\Content
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionGalleryDelete
{
    /**
     * Remove items from gallery (preview+full)
     * @param string $id
     * @param string $file
     * @throws ForbiddenException
     * @throws NativeException
     * @return string
     */
    public function galleryDelete(string $id, ?string $file = null): ?string
    {
        $this->setJsonHeader();
        if (!$file || Any::isEmpty($file)) {
            $file = (string)$this->request->query->get('file', null);
        }

        // check passed data
        if (Any::isEmpty($file) || !Any::isInt($id)) {
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
