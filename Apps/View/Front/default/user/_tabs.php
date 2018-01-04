<?php

use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;

$args = [];
if (isset($r) && Any::isStr($r) && Str::length($r) > 1)
    $args['r'] = $r;

echo \Ffcms\Core\Helper\HTML\Bootstrap\Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'login-form',
    'activeOrder' => 'action',
    'items' => [
        ['type' => 'link', 'link' => ['user/login', null, null, $args], 'text' => __('Log In')],
        ['type' => 'link', 'link' => 'user/signup', 'text' => __('Sign Up')],
        ['type' => 'link', 'link' => 'user/recovery', 'text' => __('Recovery')]
    ]
]);