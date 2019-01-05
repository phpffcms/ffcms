<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Illuminate\Support\Collection;

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
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public static function all($columns = ['*']): ?Collection
    {
        $cacheName = 'activercord.app.all.' . implode('.', $columns);
        $records = MemoryObject::instance()->get($cacheName);
        if ($records === null) {
            $records = parent::all($columns);
            MemoryObject::instance()->set($cacheName, $records);
        }

        return $records;
    }

    /**
     * Get single row by defined type and sys_name with query caching
     * @param string $type
     * @param string|array $name
     * @return self|null
     */
    public static function getItem($type, $name): ?self
    {
        foreach (self::all() as $object) {
            if ($object->type === $type) { //&& $object->sys_name === $sys_name) {
                if (Any::isArray($name) && Arr::in($object->sys_name, $name)) { // many different app name - maybe alias or something else
                    return $object;
                } elseif (Any::isStr($name) && $object->sys_name === $name) {
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
     */
    public static function getConfigs(string $type, string $name)
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
     * @return array|string|null
     */
    public static function getConfig(string $type, string $name, string $configKey)
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
     */
    public function getLocaleName(): string
    {
        if (!$this->sys_name) {
            return '';
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
    public function checkVersion(): bool
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
    public function getScriptVersion(): string
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
