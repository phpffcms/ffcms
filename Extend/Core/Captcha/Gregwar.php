<?php

namespace Extend\Core\Captcha;

use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iCaptcha;

class Gregwar implements iCaptcha
{

    /**
     * Check is captcha provide 'full-based' output realisation
     * @return bool
     */
    public function isFull()
    {
        return false;
    }

    /**
     * Get captcha image link(isFull():=false) or builded JS code(isFull():=true)
     * @return string
     */
    public function get()
    {
        return App::$Alias->scriptUrl . '/api/captcha/gregwar?time=' . microtime(true) . '&lang=' . App::$Request->getLanguage();
    }

    /**
     * Validate input data from captcha
     * @param string|null $data
     * @return bool
     */
    public static function validate($data = null)
    {
        // check if test suite is enabled and test going on
        if (App::$Properties->get('testSuite') === true && App::$Request->getClientIp() === '127.0.0.1') {
            // captcha value should be equal to config file md5 sum :)
            return $data === File::getMd5('/Private/Config/Default.php');
        }
        // allow to validate captcha by codeception tests
        $captchaValue = App::$Session->get('captcha');
        // unset session value to prevent duplication. Security fix.
        App::$Session->remove('captcha');
        // check if session has value
        if ($captchaValue === null || Str::length($captchaValue) < 1) {
            return false;
        }

        return $data === $captchaValue;
    }
}
