<?php

namespace Extend\Core\Arch;

use Apps\ActiveRecord\App as AppRecord;
use Apps\ActiveRecord\Ban;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Exception\BanException;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FrontAppController. Extended controller for front applications.
 * This controller allow to control off/on status of controller application and support fast configuration usage.
 * @package Extend\Core\Arch
 */
class FrontAppController extends Controller
{
    // information about application from table apps
    public $application;
    private $configs;

    /**
     * FrontAppController constructor. Check if app is enabled in database
     * @throws ForbiddenException
     */
    public function __construct()
    {
        // check if read ban exist
        $userId = null;
        if (App::$User->identity()) {
            $userId = App::$User->identity()->id;
        }
        if (Ban::isBanned(App::$Request->getClientIp(), $userId, false, true)) {
            throw new ForbiddenException(__("Your account is banned on website! Contact to administrator!"));
        }

        if (!$this->isEnabled()) {
            throw new ForbiddenException(__('This application is disabled or not installed!'));
        }

        // add localizations
        App::$Translate->append(App::$Alias->currentViewPath . '/I18n/' . App::$Request->getLanguage() . '.php');
        parent::__construct();
    }

    /**
     * Check is current instance of application is enabled and can be executed
     * @return bool
     */
    public function isEnabled(): bool
    {
        $appName = App::$Request->getController();
        // if app class extend current class we can get origin name
        $nativeName = Str::lastIn(get_class($this), '\\', true);
        // check if this controller is enabled
        $this->application = AppRecord::getItem('app', [$appName, $nativeName]);

        // not exist? false
        if (!$this->application) {
            return false;
        }

        // check if disabled (0 = enabled, anything else = on)
        return !(bool)$this->application->disabled;
    }

    /**
     * Get current application configs as array
     * @return array|null
     */
    public function getConfigs(): ?array
    {
        if ($this->configs !== null) {
            return $this->configs;
        }

        $configs = (array)$this->application->configs;
        foreach ($configs as $cfg => $value) {
            if (Any::isInt($value)) {
                $configs[$cfg] = $value;
            }
        }
        $this->configs = $configs;

        return $this->configs;
    }
}
