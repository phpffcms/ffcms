<?php
// check if $connectName is defined and define "null" to prevent warnings for undefined variable
if (!isset($connectName)) {
    $connectName = null;
}

// require all tables
require_once(root . '/Private/Database/Tables/App.php');
require_once(root . '/Private/Database/Tables/Blacklist.php');
require_once(root . '/Private/Database/Tables/CommentAnswer.php');
require_once(root . '/Private/Database/Tables/CommentPost.php');
require_once(root . '/Private/Database/Tables/Content.php');
require_once(root . '/Private/Database/Tables/ContentCategory.php');
require_once(root . '/Private/Database/Tables/ContentRating.php');
require_once(root . '/Private/Database/Tables/ContentTag.php');
require_once(root . '/Private/Database/Tables/FeedbackAnswer.php');
require_once(root . '/Private/Database/Tables/FeedbackPost.php');
require_once(root . '/Private/Database/Tables/Invite.php');
require_once(root . '/Private/Database/Tables/Message.php');
require_once(root . '/Private/Database/Tables/Profile.php');
require_once(root . '/Private/Database/Tables/ProfileField.php');
require_once(root . '/Private/Database/Tables/ProfileRating.php');
require_once(root . '/Private/Database/Tables/Role.php');
require_once(root . '/Private/Database/Tables/Session.php');
require_once(root . '/Private/Database/Tables/User.php');
require_once(root . '/Private/Database/Tables/UserLog.php');
require_once(root . '/Private/Database/Tables/UserProvider.php');
require_once(root . '/Private/Database/Tables/UserRecovery.php');
require_once(root . '/Private/Database/Tables/WallPost.php');
require_once(root . '/Private/Database/Tables/WallAnswer.php');

// insert demo content
require_once (root . '/Private/Database/Other/DemoContent.php');