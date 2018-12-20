<?php

namespace Extend\Core\Captcha;

use Ffcms\Core\App;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Interfaces\iCaptcha;
use Symfony\Component\HttpFoundation\Request;

class Recaptcha implements iCaptcha
{
    private static $siteKey;
    private static $secret;

    /**
     * Set site key (public key) and secret (private key) on init
     * @param string $siteKey
     * @param string $secret
     */
    public function __construct($siteKey, $secret)
    {
        self::$siteKey = $siteKey;
        self::$secret = $secret;
    }

    /**
     * Check is captcha provide 'full-based' output realisation
     * @return bool
     */
    public function isFull()
    {
        return true;
    }

    /**
     * Get captcha image link(isFull():=false) or builded JS code(isFull():=true)
     * @return string
     */
    public function get()
    {
        // build google captcha ;)
        $html = '<div class="g-recaptcha" data-sitekey="' . self::$siteKey . '"></div>
            <script type="text/javascript"
                    src="https://www.google.com/recaptcha/api.js?hl=' . App::$Request->getLanguage() . '">
            </script>';
        return $html;
    }

    /**
     * Validate input data from captcha
     * @param string|null $data
     * @return bool
     */
    public static function validate($data = null)
    {
        // nevertheless what we got in our model, recaptcha is suck and don't allow to change response field name
        $data = App::$Request->get('g-recaptcha-response');

        // make validation
        $request = Request::create('https://www.google.com/recaptcha/api/siteverify', 'GET', [
            'secret' => self::$secret,
            'response' => $data,
            'remoteip' => App::$Request->getClientIp()
        ]);

        // make request and parse response
        $url = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $response = File::getFromUrl($url);
        $object = json_decode($response);

        return $object->success;
    }
}
