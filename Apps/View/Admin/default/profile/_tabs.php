<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs nav-justified'],
    'items' => [
        ['type' => 'link', 'text' => __('Profile list'), 'link' => ['profile/index']],
        ['type' => 'link', 'text' => __('Profile fields'), 'link' => ['profile/fieldlist']],
        ['type' => 'link', 'text' => __('Settings'), 'link' => ['profile/settings']]
    ]
]);?>