<?php

namespace Apps\Model\Front\Sitemap;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Url;

/**
 * Class EntityBuildMap. Build sitemap data and save to file in xml format
 * @package Apps\Model\Front\Sitemap
 */
class EntityBuildMap extends Model
{
    private $data;
    private $langs;

    /**
     * EntityBuildMap constructor. Pass available languages and data as array inside.
     * @param array|null $langs
     * @param array|null $data
     */
    public function __construct(array $langs = null, array $data = null)
    {
        $this->langs = $langs;

        if ($data !== null && count($data) > 0) {
            foreach ($data as $item) {
                if (!Any::isArray($item) || !isset($item['uri'], $item['lastmod']))
                    continue;

                $this->add($item['uri'], $item['lastmod'], $item['freq'], $item['priority']);
            }
        }
        parent::__construct();
    }

    /**
     * Add uri/url item to sitemap builder
     * @param string $uri
     * @param int|string $lastmod
     * @param string $freq
     * @param float $priority
     */
    public function add($uri, $lastmod, $freq = 'weekly', $priority = 0.5)
    {
        // generate multi-language files
        if ($this->langs !== null && Any::isArray($this->langs) && count($this->langs) > 0) {
            foreach ($this->langs as $lang) {
                // set data to local attribute
                $this->data[$lang][] = [
                    'uri' => Url::standaloneUrl($uri, $lang),
                    'lastmod' => Date::convertToDatetime($lastmod, 'c'),
                    'freq' => (string)$freq,
                    'priority' => (float)$priority
                ];
            }
        } else { // only one language, multilanguage is disabled
            $this->data[App::$Properties->get('singleLanguage')][] = [
                'uri' => Url::standaloneUrl($uri),
                'lastmod' => Date::convertToDatetime($lastmod, 'c'),
                'freq' => (string)$freq,
                'priority' => (float)$priority
            ];
        }
    }

    /**
     * Build xml output and save it into sitemap folder
     * @param string $uniqueName
     * @return bool
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function save($uniqueName)
    {
        // check if data exists
        if ($this->data === null || !Any::isArray($this->data))
            return false;

        // list data each every language, render xml output and write into file
        foreach ($this->data as $lang => $items) {
            $xml = App::$View->render('native/sitemap_urlset', [
                'items' => $items
            ]);
            
            File::write(EntityIndexList::INDEX_PATH . '/' . $uniqueName . '.' . $lang . '.xml', $xml);
        }
        return true;
    }
}