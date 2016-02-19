<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs nav-justified'],
    'items' => [
        ['type' => 'link', 'text' => __('Feedback list'), 'link' => ['feedback/index']],
        ['type' => 'link', 'text' => __('Settings'), 'link' => ['feedback/settings']]
    ],
    'activeOrder' => 'action'
]);?>