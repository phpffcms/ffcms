<?php

namespace Apps\Controller\Api;

use Extend\Core\Arch\ApiController;
use Ffcms\Core\App;
use Gregwar\Captcha\CaptchaBuilder;

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
        $builder->build(200, 60); // build and set width/height
        // set captcha value to session
        App::$Session->set('captcha', $builder->getPhrase());

        // set header and display JPEG
        App::$Response->headers->set('Content-type', 'image/jpeg');
        $builder->output();
    }
}