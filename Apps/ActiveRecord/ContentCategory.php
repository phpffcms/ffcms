<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class ContentCategory. Active record model for content category nesting
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $path
 * @property string $title
 * @property string $description
 * @property array $configs
 * @property string $created_at
 * @property string $updated_at
 */
class ContentCategory extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'path' => 'string',
        'title' => 'serialize',
        'description' => 'serialize',
        'configs' => 'serialize'
    ];

    /**
     * Get all table rows as object
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function all($columns = ['*'])
    {
        $cacheName = 'activerecord.contentcategory.all.' . implode('.', $columns);
        $records = MemoryObject::instance()->get($cacheName);
        if ($records === null) {
            $records = parent::all($columns);
            MemoryObject::instance()->set($cacheName, $records);
        }

        return $records;
    }
    /**
     * Get record via category path address
     * @param string $path
     * @return self|object|null
     */
    public static function getByPath($path = '')
    {
        if (MainApp::$Memory->get('cache.content.category.path.' . $path) !== null) {
            return MainApp::$Memory->get('cache.content.category.path.' . $path);
        }

        $record = self::where('path', '=', $path)->first();
        MainApp::$Memory->set('cache.content.category.path.' . $path, $record);
        return $record;
    }

    /**
     * Find category by id
     * @param int $id
     * @return self|object|null
     */
    public static function getById($id)
    {
        if (MainApp::$Memory->get('cache.content.category.id.' . $id) !== null) {
            return MainApp::$Memory->get('cache.content.category.id.' . $id);
        }

        $record = self::find($id);
        MainApp::$Memory->set('cache.content.category.id.' . $id, $record);
        return $record;
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getAll()
    {
        return self::all();
    }

    /**
     * Build id-title array of sorted by nesting level categories
     * @return array
     */
    public static function getSortedCategories()
    {
        $response = [];
        $tmpData = self::getSortedAll();
        foreach ($tmpData as $path => $data) {
            $title = null;
            if (Str::likeEmpty($path)) {
                $title .= '--';
            } else {
                // set level marker based on slashes count in pathway
                $slashCount = Str::entryCount($path, '/');
                for ($i=-1; $i <= $slashCount; $i++) {
                    $title .= '--';
                }
            }
            // add canonical title from db
            $title .= ' ' . $data->getLocaled('title');
            // set response as array [id => title, ... ]
            $response[$data->id] = $title;
        }

        return $response;
    }

    /**
     * Get all categories sorted by pathway
     * @return array
     */
    public static function getSortedAll()
    {
        $list = self::all();
        $response = [];
        foreach ($list as $row) {
            $response[$row->path] = $row;
        }
        ksort($response);
        return $response;
    }

    /**
     * Get property by key of current category
     * @param string $key
     * @return bool|string|null
     */
    public function getProperty($key)
    {
        $properties = $this->configs;
        // check if properties is defined
        if (!Any::isArray($properties) || !array_key_exists($key, $properties)) {
            return false;
        }

        return $properties[$key];
    }

    /**
     * Get parent category object
     * @return ContentCategory|null|object
     */
    public function getParent()
    {
        $path = $this->path;
        if (!Str::contains('/', $path)) {
            return null;
        }

        $arr = explode('/', $path);
        array_pop($arr);
        $parentPath = trim(implode('/', $arr), '/');
        return self::getByPath($parentPath);
    }
}