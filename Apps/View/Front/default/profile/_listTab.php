<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'activeOrder' => 'id',
    'items' => [
        ['type' => 'link', 'link' => ['profile/index', 'all'], 'text' => __('All')],
        ['type' => 'link', 'link' => ['profile/index', 'rating'], 'text' => __('Rating')],
        ['type' => 'link', 'link' => ['profile/search'], 'text' => __('Search')]
    ]
]);
?>