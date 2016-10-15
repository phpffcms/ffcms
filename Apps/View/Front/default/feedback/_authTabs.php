<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;

if (!\App::$User->isAuth()) {
    return null;
}
?>
<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'items' => [
        ['type' => 'link', 'text' => __('New request'), 'link' => 'feedback/create'],
        ['type' => 'link', 'text' => __('My requests'), 'link' => 'feedback/list']
    ]
]);