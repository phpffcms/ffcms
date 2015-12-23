<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class App extends ActiveModel
{

    /**
     * Get all objects with query caching
     * @return \Illuminate\Database\Eloquent\Collection|static
     * @throws SyntaxException
     */
    public static function getAll()
    {
        $object = MemoryObject::instance()->get('app.cache.all');
        // empty?
        if ($object === null) {
            $object = self::all();
            MemoryObject::instance()->set('app.cache.all', $object);
        }

        if ($object === null) {
            throw new SyntaxException('Application table "prefix_app" is empty!!!');
        }

        return $object;
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
        foreach (self::getAll() as $object) {
            if ($object['type'] === $type) {
                $response[] = $object;
            }
        }

        return $response;
    }

    /**
     * Get single row by defined type and sys_name with query caching
     * @param string $type
     * @param string $sys_name
     * @return mixed|null
     * @throws SyntaxException
     */
    public static function getItem($type, $sys_name)
    {
        foreach (self::getAll() as $object) {
            if ($object['type'] === $type && $object['sys_name'] === $sys_name) {
                return $object;
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
        foreach (self::getAll() as $row) {
            if ($row->type === $type && $row->sys_name === $name) {
                return Serialize::decode($row->configs);
            }
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

        $nameObject = Serialize::decode($this->name);
        $lang = \Ffcms\Core\App::$Request->getLanguage();
        $name = $nameObject[$lang];
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

        $class = 'Apps\Controller\Admin\\' . $this->sys_name;
        if (!class_exists($class)) {
            return false;
        }

        if (!defined($class . '::VERSION')) {
            return false;
        }

        return (float)constant($class.'::VERSION') === (float)$this->version;
    }

}