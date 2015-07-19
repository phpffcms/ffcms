<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\App;
use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\String;

class ContentCategory extends ActiveModel
{
    /**
     * Get record via category path address
     * @param string $path
     * @return self|object|null
     */
    public static function getByPath($path = '')
    {
        if (App::$Memory->get('cache.content.category.path.' . $path) !== null) {
            return App::$Memory->get('cache.content.category.path.' . $path);
        }

        $record = self::where('path', '=', $path)->first();
        App::$Memory->set('cache.content.category.path.' . $path, $record);
        return $record;
    }

    /**
     * Find category by id
     * @param int $id
     * @return self|object|null
     */
    public static function getById($id)
    {
        if (App::$Memory->get('cache.content.category.id.' . $id) !== null) {
            return App::$Memory->get('cache.content.category.id.' . $id);
        }

        $record = self::find($id);
        App::$Memory->set('cache.content.category.id.' . $id, $record);
        return $record;
    }

    /**
     * Get all rows as object
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getAll()
    {
        if (App::$Memory->get('cache.content.category.all') !== null) {
            return App::$Memory->get('cache.content.category.all');
        }

        $record = self::all();
        App::$Memory->set('cache.content.category.all', $record);
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
            if (String::likeEmpty($path)) {
                $title .= '--';
            } else {
                // set level marker based on slashes count in pathway
                $slashCount = String::entryCount($path, '/');
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
        if ($properties === null || String::length($properties) < 1) {
            return false;
        }

        $properties = Serialize::decode($properties);
        return $properties[$key];
    }

}