<?php

namespace Apps\Controller\Admin;


use Extend\Core\Arch\AdminController;

/**
 * Class User. Admin controller of user application.
 * @package Apps\Controller\Admin
 */
class User extends AdminController
{
    const VERSION = '1.0.1';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    use User\ActionIndex {
        index as actionIndex;
    }

    use User\ActionUpdate {
        update as actionUpdate;
    }

    use User\ActionDelete {
        delete as actionDelete;
    }

    use User\ActionClear {
        clear as actionClear;
    }

    use User\ActionInviteList {
        inviteList as actionInvitelist;
    }

    use User\ActionInviteSend {
        inviteSend as actionInvite;
    }

    use User\ActionInviteDelete {
        inviteDelete as actionInviteDelete;
    }

    use User\ActionRoleList {
        listing as actionRolelist;
    }

    use User\ActionRoleUpdate {
        roleUpdate as actionRoleupdate;
    }

    use User\ActionSettings {
        settings as actionSettings;
    }

    use User\ActionApprove {
        approve as actionApprove;
    }
}
