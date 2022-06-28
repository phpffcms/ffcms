<?php

namespace Apps\Controller\Api;

use Apps\ActiveRecord\Spam;
use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Gregwar\Captcha\CaptchaBuilder;

/**
 * Class Captcha. Captcha API.
 * @package Apps\Controller\Api
 */
class Captcha extends ApiController
{
    /**
     * Build gregwar captcha image
     */
    public function actionGregwar()
    {
        // set header. Class response->header->set is not working here (content output directly before)
        header('Content-type: image/jpeg');
        $builder = new CaptchaBuilder();
        $builder->setDistortion(true);
        $builder->build(mt_rand(200, 250), mt_rand(70, 80)); // build and set random width/height
        // set captcha value to session
        App::$Session->set('captcha', $builder->getPhrase());

        // set header and display JPEG
        $this->response->headers->set('Content-type', 'image/jpeg');
        $builder->output();
    }

    /**
     * Check if captcha verification required for current user
     * @param string|null $token
     * @return string|null
     */
    public function actionVerify($token = null): ?string
    {
        $this->setJsonHeader();

        // check if smart features enabled
        $settings = App::$Properties->get('captcha');
        if (!$settings || !$settings['smart']) {
            return json_encode([
                'required' => true
            ]);
        }

        $ip = $this->request->getClientIp();
        $userId = null;
        if (App::$User->isAuth()) {
            $userId = App::$User->identity()->getId();
        }

        $record = Spam::activity($ip, $userId);

        return json_encode([
            'required' => $record->isThresholdReached()
        ]);
    }
}
