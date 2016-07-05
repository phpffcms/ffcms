<?php

namespace Apps\Model\Front\Sitemap;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class EntityIndexList. List sitemap files in special directory
 * @package Apps\Model\Front\Sitemap
 */
class EntityIndexList extends Model
{
    const INDEX_PATH = '/upload/sitemap';

    public $files;
    private $info;

    private $_lang;

    /**
     * EntityIndexList constructor. Pass current language from controller request
     * @param bool $currentLang
     */
    public function __construct($currentLang)
    {
        $this->_lang = $currentLang;
        parent::__construct();
    }

    /**
     * Try to find sitemap indexes in storage directory
     * @throws SyntaxException
     */
    public function before()
    {
        if (!Directory::exist(static::INDEX_PATH)) {
            throw new SyntaxException(__('Directory %dir% for sitemaps is not exists', ['dir' => static::INDEX_PATH]));
        }

        $scan = File::listFiles(static::INDEX_PATH, ['.xml'], true);
        if (Obj::isArray($scan)) {
            foreach ($scan as $file) {
                if (!Str::contains('.' . $this->_lang, $file)) {
                    continue;
                }
                $this->files[] = static::INDEX_PATH . '/' . $file;
            }
        }
    }

    /**
     * Build sitemap index files information - location, last modify time
     */
    public function make()
    {
        if (!Obj::isArray($this->files)) {
            return;
        }

        // build file information data
        foreach ($this->files as $file) {
            $this->info[] = [
                'loc' => App::$Alias->scriptUrl . $file,
                'lastmod' => Date::convertToDatetime(File::mTime($file), 'c')
            ];
        }
    }

    /**
     * Get sitemap index files info as array
     * @return array|null
     */
    public function getInfo()
    {
        return $this->info;
    }
}