<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;

?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs nav-justified'],
    'items' => [
        ['type' => 'link', 'text' => __('Content list'), 'link' => ['content/index']],
        ['type' => 'link', 'text' => __('Categories manage'), 'link' => ['content/categories']],
        ['type' => 'link', 'text' => __('Settings'), 'link' => ['content/settings']]
    ],
    'activeOrder' => 'action'
]);?>