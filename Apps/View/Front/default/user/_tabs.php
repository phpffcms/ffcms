<?php
echo \Ffcms\Core\Helper\HTML\Bootstrap\Nav::display([
    'ul' => 'nav-tabs',
    'tabAnchor' => 'login-form',
    'items' => [
        ['type' => 'link', 'link' => 'user/login', 'text' => __('Log In')],
        ['type' => 'link', 'link' => 'user/signup', 'text' => __('Sign Up')],
        ['type' => 'link', 'link' => 'user/recovery', 'text' => __('Recovery')]
    ]
]);