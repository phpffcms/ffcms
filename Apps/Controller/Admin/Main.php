<?php

namespace Apps\Controller\Admin;


use Extend\Core\Arch\AdminController;

/**
 * Class Main. Admin main controller - index page, settings, file manager, security and etc.
 * @package Apps\Controller\Admin
 */
class Main extends AdminController
{
    public $type = 'app';

    // import heavy actions
    use Main\ActionIndex {
        index as actionIndex;
    }

    use Main\ActionSettings {
        settings as actionSettings;
    }

    use Main\ActionUpdates {
        updates as actionUpdates;
    }

    use Main\ActionSessions {
        sessions as actionSessions;
    }

    use Main\ActionFiles {
        files as actionFiles;
    }

    use Main\ActionAntivirus {
        antivirus as actionAntivirus;
    }

    use Main\ActionDebugCookie {
        debugCookie as actionDebugcookie;
    }

    use Main\ActionRouting {
        routing as actionRouting;
    }

    use Main\ActionAddRoute {
        addRoute as actionAddroute;
    }

    use Main\ActionDeleteRoute {
        deleteRoute as actionDeleteroute;
    }

    use Main\ActionCache {
        cache as actionCache;
    }

    /**
     * Main constructor. Disable parent inheritance of typical app version checking
     */
    public function __construct()
    {
        parent::__construct(false);
    }

}
