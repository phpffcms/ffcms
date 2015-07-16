<?php

echo \Ffcms\Core\Helper\HTML\Bootstrap\Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'settings-tb',
    'activeOrder' => 'action',
    'items' => [
        ['type' => 'link', 'link' => 'profile/avatar', 'text' => __('Avatar')],
        ['type' => 'link', 'link' => 'profile/settings', 'text' => __('Profile')],
        ['type' => 'link', 'link' => 'profile/password', 'text' => __('Password')],
        ['type' => 'link', 'link' => 'profile/ignore', 'text' => __('Blacklist')]
    ]
]);