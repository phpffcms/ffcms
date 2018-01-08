<?php

namespace Extend\Core\Arch;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;

/**
 * Class ApiController. Native controller to extend it by apps api controllers
 * @package Extend\Core\Arch
 */
class ApiController extends Controller
{

    /**
     * ApiController constructor. Disable global layout for api responses
     */
    public function __construct()
    {
        $this->layout = null;
        parent::__construct();
    }

    /**
     * Set json header to http transport
     */
    public function setJsonHeader()
    {
        $this->response->headers->set('Content-Type', 'application/json');
    }
}
