<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App as MainApp;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;

class ContentCategory extends ActiveModel
{
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
     * Get all rows as object
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getAll()
    {
        if (MainApp::$Memory->get('cache.content.category.all') !== null) {
            return MainApp::$Memory->get('cache.content.category.all');
        }

        $record = self::all();
        MainApp::$Memory->set('cache.content.category.all', $record);
        return $record;
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
            $title .= ' ' . Serialize::getDecodeLocale($data->title);
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
        $list = self::getAll();
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
        if ($properties === null || Str::length($properties) < 1) {
            return false;
        }

        $properties = Serialize::decode($properties);
        return $properties[$key];
    }

}