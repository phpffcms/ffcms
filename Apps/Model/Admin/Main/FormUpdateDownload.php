<?php

namespace Apps\Model\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;

/**
 * Class FormUpdateDownload. Download update from remote url and extract it in root directory
 * @package Apps\Model\Admin\Main
 */
class FormUpdateDownload extends Model
{
    private $url;
    private $name;

    /**
     * FormUpdateDownload constructor. Pass download url & tag name inside
     * @param bool $url
     * @param $name
     */
    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
        parent::__construct();
    }

    /**
     * Download archive and extract to root directory
     * @return bool
     */
    public function make()
    {
        $archive = $this->name . '.zip';
        // download archive
        File::saveFromUrl($this->url, '/' . $archive);
        // extract archive
        $zip = new \ZipArchive();
        if ($zip->open(root . '/' . $archive) === true) {
            $zip->extractTo(root);
            $zip->close();
            // cleanup cache
            App::$Cache->clean();
            return true;
        }

        return false;
    }
}