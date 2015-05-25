<?php
use Ffcms\Core\Helper\Url;

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('profile/show', $user->id) => __('Profile'),
    __('Avatar settings')
];
?>

<h1>Avatar change</h1>
<hr />