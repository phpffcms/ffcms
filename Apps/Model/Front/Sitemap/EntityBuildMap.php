<?php

namespace Apps\Model\Front\Sitemap;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Templex\Url\Url;

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
    public function __construct(?array $langs = null, ?array $data = null)
    {
        $this->langs = $langs;

        if ($data && count($data) > 0) {
            foreach ($data as $item) {
                if (!Any::isArray($item) || !isset($item['uri'], $item['lastmod'])) {
                    continue;
                }

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
     * @param string|null $title
     */
    public function add(?string $uri, $lastmod, string $freq = 'weekly', float $priority = 0.5, ?string $title = null)
    {
        // generate multi-language files
        if ($this->langs && Any::isArray($this->langs) && count($this->langs) > 0) {
            foreach ($this->langs as $lang) {
                // set data to local attribute
                $this->data[$lang][] = [
                    'uri' => Url::stringUrl($uri, $lang),
                    'lastmod' => Date::convertToDatetime($lastmod, 'c'),
                    'freq' => (string)$freq,
                    'priority' => (float)$priority,
                    'title' => $title
                ];
            }
        } else { // only one language, multilanguage is disabled
            $this->data[App::$Properties->get('singleLanguage')][] = [
                'uri' => Url::stringUrl($uri),
                'lastmod' => Date::convertToDatetime($lastmod, 'c'),
                'freq' => (string)$freq,
                'priority' => (float)$priority,
                'title' => $title
            ];
        }
    }

    /**
     * Build xml output and save it into sitemap folder
     * @param string $uniqueName
     * @return bool
     */
    public function save(string $uniqueName): bool
    {
        // check if data exists
        if (!$this->data || !Any::isArray($this->data)) {
            return false;
        }

        // list data each every language, render xml output and write into file
        foreach ($this->data as $lang => $items) {
            $xml = App::$View->render('sitemap/urlset', [
                'items' => $items
            ]);

            // write xml output
            File::write(EntityIndexList::INDEX_PATH . '/' . $uniqueName . '.' . $lang . '.xml', $xml);
            // write json output for html map
            File::write(EntityIndexList::INDEX_PATH . '/' . $uniqueName . '.' . $lang . '.json', json_encode($items));
        }
        return true;
    }
}
