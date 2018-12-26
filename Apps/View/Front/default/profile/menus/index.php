<?php

/** @var bool $rating */
/** @var \Ffcms\Templex\Template\Template $this */

$menu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
$menu->menu(['link' => ['profile/index', ['all']], 'text' => __('All')]);
if ($rating) {
    $menu->menu(['link' => ['profile/index', ['rating']], 'text' => __('Rating')]);
}
$menu->menu(['link' => ['profile/search'], 'text' => __('Search')]);
$menu->menu(['link' => ['profile/feed'], 'text' => __('Feed')]);
echo $menu->display();