<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class App - active record for 'prefix_apps' table.
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $type
 * @property string $sys_name
 * @property string $name
 * @property string $configs
 * @property bool $disabled
 * @property string $version
 * @property string $created_at
 * @property string $updated_at
 */
class App extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'type' => 'string',
        'sys_name' => 'string',
        'name' => 'serialize',
        'configs' => 'serialize',
        'disabled' => 'boolean',
        'version' => 'string'
    ];

    /**
     * Get all objects with query caching
     * @param $columns array
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws SyntaxException
     */
    public static function all($columns = ['*'])
    {
        $cacheName = 'activercord.app.all.' . implode('.', $columns);
        $records = MemoryObject::instance()->get($cacheName);
        if ($records === null) {
            $records = parent::all($columns);
            MemoryObject::instance()->set($cacheName, $records);
        }

        if ($records === null) {
            throw new SyntaxException('Applications is not found in table "prefix_apps"!');
        }
        return $records;
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     * @throws SyntaxException
     */
    public static function getAll()
    {
        return self::all();
    }

    /**
     * Get all object by defined $type with caching query in memory
     * @param $type
     * @return array|null
     * @throws SyntaxException
     */
    public static function getAllByType($type)
    {
        $response = null;
        foreach (self::all() as $object) {
            if ($object->type === $type) {
                $response[] = $object;
            }
        }

        return $response;
    }

    /**
     * Get single row by defined type and sys_name with query caching
     * @param string $type
     * @param string|array $sys_name
     * @return mixed|null
     * @throws SyntaxException
     */
    public static function getItem($type, $sys_name)
    {
        foreach (self::all() as $object) {
            if ($object->type === $type) { //&& $object->sys_name === $sys_name) {
                if (Obj::isArray($sys_name) && Arr::in($object->sys_name, $sys_name)) { // many different app name - maybe alias or something else
                    return $object;
                } elseif (Obj::isString($sys_name) && $object->sys_name === $sys_name) {
                    return $object;
                }
            }
        }

        return null;
    }

    /**
     * Get application configs
     * @param string $type
     * @param string $name
     * @return array|null|string
     * @throws SyntaxException
     */
    public static function getConfigs($type, $name)
    {
        foreach (self::all() as $row) {
            if ($row->type === $type && $row->sys_name === $name) {
                return $row->configs;
            }
        }

        return null;
    }

    /**
     * Get single config value by ext type, ext name and config key
     * @param string $type
     * @param string $name
     * @param string $configKey
     * @return null
     */
    public static function getConfig($type, $name, $configKey)
    {
        $configs = self::getConfigs($type, $name);
        if (isset($configs[$configKey])) {
            return $configs[$configKey];
        }

        return null;
    }

    /**
     * Get localized application name
     * @return string
     * @throws SyntaxException
     */
    public function getLocaleName()
    {
        if ($this->sys_name === null) {
            throw new SyntaxException('Application object is not founded');
        }

        $name = $this->getLocaled('name');
        if (Str::likeEmpty($name)) {
            $name = $this->sys_name;
        }
        return $name;
    }

    /**
     * Check if app version match db version of this app
     * @return bool
     * @throws SyntaxException
     */
    public function checkVersion()
    {
        if ($this->sys_name === null) {
            throw new SyntaxException('Application object is not founded');
        }

        $scriptVersion = $this->getScriptVersion();

        return version_compare($scriptVersion, $this->version) === 0;
    }

    /**
     * Get extension script version if exists
     * @return string
     */
    public function getScriptVersion()
    {
        $class = 'Apps\Controller\Admin\\' . $this->sys_name;
        if (!class_exists($class)) {
            return false;
        }

        if (!defined($class . '::VERSION')) {
            return false;
        }

        return constant($class . '::VERSION');
    }

}