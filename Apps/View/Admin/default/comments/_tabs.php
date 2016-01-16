<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs nav-justified'],
    'items' => [
        ['type' => 'link', 'text' => __('Comments list'), 'link' => ['comments/index']],
        ['type' => 'link', 'text' => __('Settings'), 'link' => ['comments/settings']]
    ]
]);?>