<?php
/** @var \Ffcms\Templex\Template\Template $this */
?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs'])
    ->menu(['text' => __('User list'), 'link' => ['user/index']])
    ->menu(['text' => __('Role management'), 'link' => ['user/rolelist']])
    ->menu(['text' => __('Settings'), 'link' => ['user/settings']])
    ->display(); ?>
