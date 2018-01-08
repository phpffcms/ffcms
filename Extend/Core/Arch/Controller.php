<?php

namespace Extend\Core\Arch;

use Ffcms\Core\App;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Arch\Controller as AbstractController;

class Controller extends AbstractController
{
    protected $table;

    public function __construct()
    {
        $this->loadAppsTable();
        parent::__construct();
    }

    /**
     * Load application table in memory & cache. All applications as object is available in $this->apps
     */
    private function loadAppsTable()
    {
        if (App::$Memory->get('table.apps') !== null) {
            $this->table = App::$Memory->get('table.apps');
        } else {
            $this->table = AppRecord::all();
            App::$Memory->set('table.apps', $this->table);
        }
    }
}
