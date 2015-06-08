<?php

namespace Extend\Core\Arch;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;

class ApiController extends Controller
{

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
        App::$Response->headers->set('Content-Type', 'application/json');
    }

}