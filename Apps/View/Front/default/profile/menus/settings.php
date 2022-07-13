<?php

/** @var \Ffcms\Templex\Template\Template $this */

echo $this->bootstrap()->nav('ul', ['class' => 'nav nav-tabs'])
    ->menu(['link' => ['profile/avatar'], 'text' => __('Photo')])
    ->menu(['link' => ['profile/settings'], 'text' => __('Profile')])
    ->menu(['link' => ['profile/password'], 'text' => __('Password')])
    ->menu(['link' => ['profile/ignore'], 'text' => __('Blacklist')])
    ->menu(['link' => ['profile/log'], 'text' => __('Logs')])

?>