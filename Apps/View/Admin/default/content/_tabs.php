<?php
/** @var \Ffcms\Templex\Template\Template $this */
?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Contents'), 'link' => ['content/index']])
    ->menu(['text' => __('Categories'), 'link' => ['content/categories']])
    ->menu(['text' => __('Settings'), 'link' => ['content/settings']])
    ->display(); ?>