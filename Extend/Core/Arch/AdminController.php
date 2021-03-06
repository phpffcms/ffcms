<?php

namespace Extend\Core\Arch;

use Ffcms\Core\App;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class AdminController - class to extend classic admin controllers by extension type.
 * Used: access security control, application listing, widget listing, current extension data
 * @package Extend\Core\Arch
 */
class AdminController extends Controller
{
    public $type = 'app';

    /** @var array $applications */
    protected $applications;
    /** @var array $widgets */
    protected $widgets;

    /** @var AppRecord $application */
    protected $application;
    /** @var AppRecord $widget */
    protected $widget;

    /**
     * AdminController constructor.
     * @param bool $checkVersion
     * @throws ForbiddenException
     */
    public function __construct(bool $checkVersion = true)
    {
        parent::__construct();
        $this->buildExtensions();
        $this->checkAccess();

        // if version is not necessary to check - continue
        if (!$checkVersion) {
            return;
        }

        // get extension record based on type
        $record = $this->getTypeItem();

        // check if extension is loaded
        if (!$record) {
            throw new ForbiddenException(__('This extension is not installed'));
        }

        // check extension version
        if (!method_exists($record, 'checkVersion') || $record->checkVersion() !== true) {
            App::$Session->getFlashBag()->add(
                'error',
                __('Attention! Version of this extension scripts is no match to database version. Please, make update!')
            );
        }
    }

    /**
     * Build apps/widgets table in local property
     */
    private function buildExtensions()
    {
        $controller = Str::lastIn(get_class($this), '\\', true);
        foreach ($this->table as $item) {
            if ($item->type === 'app') {
                $this->applications[] = $item;
                if ($this->type === 'app' && $item->sys_name === $controller) {
                    $this->application = $item;
                }
            } elseif ($item->type === 'widget') {
                $this->widgets[] = $item;
                if ($this->type === 'widget' && $item->sys_name === $controller) {
                    $this->widget = $item;
                }
            }
        }
    }

    /**
     * Check if current user can access to admin controllers
     * @throws ForbiddenException
     */
    private function checkAccess()
    {
        $user = App::$User->identity();
        // user is not authed ?
        if (!$user || !App::$User->isAuth()) {
            $redirectUrl = App::$Alias->scriptUrl . '/user/login';
            App::$Response->redirect($redirectUrl, true);
            exit();
        }

        $permission = env_name . '/' . App::$Request->getController() . '/' . App::$Request->getAction();
        // doesn't have permission? get the f*ck out
        if (!$user->role->can($permission)) {
            throw new ForbiddenException(__('You got no access rights to this page'));
        }
    }

    /**
     * Get all extensions as table active record
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get all extensions as active records by current type
     * @param string|null $type
     * @return mixed
     */
    public function getTypeTable($type = null)
    {
        if (!$type) {
            $type = $this->type;
        }

        return $type === 'widget' ? $this->widgets : $this->applications;
    }

    /**
     * Get current extension active record
     * @param string|null $type
     * @return mixed
     */
    public function getTypeItem($type = null)
    {
        if (!$type) {
            $type = $this->type;
        }

        return $type === 'widget' ? $this->widget : $this->application;
    }

    /**
     * Get current application configs as array
     * @return array
     */
    public function getConfigs(): ?array
    {
        $configs = $this->type === 'widget' ? (array)$this->widget->configs : (array)$this->application->configs;
        foreach ($configs as $cfg => $value) {
            if (Any::isInt($value)) {
                $configs[$cfg] = $value;
            }
        }
        return $configs;
    }

    /**
     * Save extension configs
     * @param array $configs
     * @return bool
     */
    public function setConfigs(array $configs = null): bool
    {
        if ($configs === null || !Any::isArray($configs) || count($configs) < 1) {
            return false;
        }

        // get extension is based on it type
        $id = 0;
        if ($this->type === 'app') {
            $id = $this->application->id;
        } elseif ($this->type === 'widget') {
            $id = $this->widget->id;
        }

        // get active record relation for this id
        $obj = \Apps\ActiveRecord\App::find($id);

        if (!$obj) {
            return false;
        }

        // save data in db
        $obj->configs = $configs;
        $obj->save();
        return true;
    }
}
