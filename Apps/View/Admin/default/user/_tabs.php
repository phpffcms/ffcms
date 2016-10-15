<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;

?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs nav-justified'],
    'items' => [
        ['type' => 'link', 'text' => __('User list'), 'link' => ['user/index']],
        ['type' => 'link', 'text' => __('Group management'), 'link' => ['user/grouplist']],
        ['type' => 'link', 'text' => __('Settings'), 'link' => ['user/settings']]
    ]
]);?>