<?php

namespace Extend\Core\Captcha;

use Ffcms\Core\App;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\String;
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
     * @throws SyntaxException
     */
    public static function validate($data = null)
    {
        $captchaValue = App::$Session->get('captcha');
        if ($captchaValue === null || String::length($captchaValue) < 1) {
            return false;
        }

        return $data === $captchaValue;
    }
}