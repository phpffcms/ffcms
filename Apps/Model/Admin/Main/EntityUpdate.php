<?php

namespace Apps\Model\Admin\Main;

use Apps\ActiveRecord\System;
use Extend\Version;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;

/**
 * Class EntityUpdate. Update entity business logic - update checks & statistic collection
 * @package Apps\Model\Admin\Main
 */
class EntityUpdate extends Model
{
    const API_LATEST_RELEASE = 'https://api.github.com/repos/phpffcms/ffcms/releases/latest';
    public static $apiZipTypes = [
        'application/zip',
        'application/x-zip-compressed'
    ];

    public $scriptVersion;
    public $dbVersion;
    public $versionsEqual = false;

    public $lastVersion;
    public $lastInfo = [
        'name' => null,
        'created_at' => null,
        'download_url' => null
    ];

    public $haveRemoteNew = false;

    /**
     * Set default model properties
     */
    public function before()
    {
        $this->scriptVersion = Version::VERSION;
        $this->dbVersion = System::getVar('version')['data'];

        $this->versionsEqual = (version_compare($this->scriptVersion, $this->dbVersion) === 0);
        $this->findLatestVersion();
        $this->haveRemoteNew = ($this->lastVersion !== null && version_compare($this->scriptVersion, $this->lastVersion) === -1);
    }

    /**
     * Find latest release in github API and get required info
     */
    private function findLatestVersion()
    {
        // get remote api with json response
        $gitJson = File::getFromUrl(static::API_LATEST_RELEASE);
        if (!$gitJson) {
            return;
        }

        // parse api response to model attributes
        $git = json_decode($gitJson, true);
        $this->lastVersion = $git['tag_name'];
        // get download url to full compiled distributive (uploaded to each release as .zip archive, placed in release.assets)
        $download = null;
        if (Any::isArray($git['assets'])) {
            foreach ($git['assets'] as $asset) {
                if (Arr::in($asset['content_type'], static::$apiZipTypes) && $asset['state'] === 'uploaded') {
                    $download = $asset['browser_download_url'];
                }
            }
        }
        $this->lastInfo = [
            'name' => $git['name'],
            'created_at' => Date::convertToDatetime($git['published_at'], Date::FORMAT_TO_HOUR),
            'download_url' => $download
        ];
    }
}
