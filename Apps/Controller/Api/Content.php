<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\App as AppRecord;
use Extend\Core\Arch\ApiController;

/**
 * Class Content. Content app backend json api
 * @package Apps\Controller\Api
 */
class Content extends ApiController
{
    public $maxSize = 512000; // in bytes, 500 * 1024
    public $maxResize = 150;

    public $allowedExt = ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'webp'];

    // import actions from traits
    use Content\ActionChangeRate {
        changeRate as actionChangerate;
    }

    use Content\ActionGalleryUpload {
        galleryUpload as actionGalleryupload;
    }

    use Content\ActionGalleryList {
        galleryList as actionGallerylist;
    }

    use Content\ActionGalleryDelete {
        galleryDelete as actionGallerydelete;
    }

    /**
     * Prepare configurations before initialization
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
}
